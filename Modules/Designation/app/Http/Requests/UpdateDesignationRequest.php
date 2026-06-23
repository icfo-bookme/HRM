<?php

namespace Modules\Designation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $designationId = $this->route('designation');

        return [
           
            'department_id' => 'required|integer|exists:departments,id',
            'grade_id'      => 'nullable|integer|exists:salary_grades,id',
           
            'title'         => 'required|string|max:200',
            'level'         => 'nullable|integer|min:1|max:99',
             'responsibilities'   => 'nullable|array',
            'responsibilities.*' => 'string|distinct|max:500',
            
            'requirements'       => 'nullable|array',
            'requirements.*'     => 'string|distinct|max:500',
            'is_active'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Department is required.',
            'department_id.exists'   => 'Selected department does not exist.',
            'title.required'         => 'Designation title is required.',
            'grade_id.exists'        => 'Selected salary grade does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'responsibilities' => $this->normalizeArrayField($this->responsibilities),
            'requirements'     => $this->normalizeArrayField($this->requirements),
        ]);
    }

    protected function normalizeArrayField($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $lines = preg_split('/\r?\n/', trim($value));
        return collect($lines)->filter()->values()->all();
    }
}
