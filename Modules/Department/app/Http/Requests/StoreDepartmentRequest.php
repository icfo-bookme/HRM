<?php

namespace Modules\Department\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
        return [
            'company_id'      => 'nullable|integer|exists:companies,id',
            'branch_id'       => 'required|integer|exists:branches,id',
            'cost_center_id'  => 'nullable|integer',
            'parent_id'       => 'nullable|integer|exists:departments,id|different:id',
            'code'            => 'nullable|string|max:50|unique:departments,code',
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
            'branch_id.required'      => 'Branch is required.',
            'branch_id.exists'        => 'The selected branch does not exist.',
            'company_id.required'      => 'Company is required.',
            'company_id.exists'        => 'The selected company does not exist.',
            'code.required'            => 'Department code is required.',
            'code.unique'              => 'Department code must be unique.',
            'name.required'            => 'Department name is required.',
            'parent_id.exists'         => 'The selected parent department does not exist.',
            'parent_id.different'      => 'Parent department cannot be the same as current department.',
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
