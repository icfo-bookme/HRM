<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBasicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['required', 'string', 'max:50'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'grade_id' => ['nullable', 'exists:salary_grades,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'reports_to' => ['nullable', 'exists:employees,id'],
            'employment_type' => ['nullable', 'string', 'max:50'],
            'joining_date' => ['nullable', 'date'],
            'confirmation_date' => ['nullable', 'date'],
            'probation_end_date' => ['nullable', 'date'],
            'last_working_day' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'portal_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_code.required' => 'Employee code is required.',
            'branch_id.exists' => 'Selected branch is invalid.',
            'department_id.exists' => 'Selected department is invalid.',
            'designation_id.exists' => 'Selected designation is invalid.',
        ];
    }
}
