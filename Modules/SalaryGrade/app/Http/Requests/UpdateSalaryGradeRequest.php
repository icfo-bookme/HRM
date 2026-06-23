<?php

namespace Modules\SalaryGrade\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalaryGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $salaryGradeId = $this->route('salarygrade');

        return [
            
            'name'       => 'required|string|max:200',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|gte:min_salary',
            'currency'   => 'required|string|max:10',
            'is_active'  => 'nullable|boolean',
            'metadata'   => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
           
            'name.required'       => 'Salary grade name is required.',
            'min_salary.required' => 'Minimum salary is required.',
            'max_salary.required' => 'Maximum salary is required.',
            'max_salary.gte'      => 'Maximum salary must be greater than or equal to minimum salary.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('metadata') && is_string($this->metadata)) {
            $this->merge(['metadata' => json_decode($this->metadata, true)]);
        }
    }
}
