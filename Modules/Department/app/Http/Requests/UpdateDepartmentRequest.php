<?php

namespace Modules\Department\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $departmentId = $this->route('department');

        return [
           
            'branch_id'       => 'nullable|integer|exists:branches,id',
            'cost_center_id'  => 'nullable|integer',
            'parent_id'       => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id'),
                Rule::notIn([$departmentId]), // Cannot be parent to itself
            ],
            'code'            => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($departmentId),
            ],
            'name'            => 'required|string|max:200',
            'description'     => 'nullable|string|max:500',
            'head_employee_id' => 'nullable|integer|exists:users,id',
            'email'           => 'nullable|email|max:150',
            'phone'           => 'nullable|string|max:20',
            'is_active'       => 'nullable|boolean',
            'sort_order'      => 'nullable|integer|min:0|max:999',
            'metadata'        => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'branch_id.exists'        => 'The selected branch does not exist.',
            'name.required'            => 'Department name is required.',
            'parent_id.exists'         => 'The selected parent department does not exist.',
            'parent_id.not_in'         => 'Parent department cannot be the same as current department.',
            'head_employee_id.exists'  => 'The selected employee does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('metadata') && is_string($this->metadata)) {
            $this->merge([
                'metadata' => json_decode($this->metadata, true),
            ]);
        }
    }
}
