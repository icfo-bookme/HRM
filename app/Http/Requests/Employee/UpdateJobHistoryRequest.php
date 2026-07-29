<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:employee_job_history,id'],
            'effective_date' => ['required', 'date'],
            'change_type' => ['required', 'string', 'max:100'],
            'from_desig_id' => ['nullable', 'exists:designations,id'],
            'to_desig_id' => ['nullable', 'exists:designations,id'],
            'from_dept_id' => ['nullable', 'exists:departments,id'],
            'to_dept_id' => ['nullable', 'exists:departments,id'],
            'from_branch_id' => ['nullable', 'exists:branches,id'],
            'to_branch_id' => ['nullable', 'exists:branches,id'],
            'from_grade_id' => ['nullable', 'exists:salary_grades,id'],
            'to_grade_id' => ['nullable', 'exists:salary_grades,id'],
            'from_salary' => ['nullable', 'numeric'],
            'to_salary' => ['nullable', 'numeric'],
            'reason' => ['nullable', 'string', 'max:500'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'effective_date.required' => 'Effective date is required.',
            'change_type.required' => 'Change type is required.',
        ];
    }
}
