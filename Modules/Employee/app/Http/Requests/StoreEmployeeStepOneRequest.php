<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepOneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'employee_code' => 'required|string|max:50',
            'company_id' => 'nullable|integer|exists:companies,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'department_id' => 'required|integer|exists:departments,id',
            'designation_id' => 'required|integer|exists:designations,id',
            'grade_id' => 'nullable|integer|exists:salary_grades,id',
            'shift_id' => 'nullable|integer|exists:shifts,id',
            'reports_to' => 'nullable|integer|exists:employees,id',
            'employment_type' => 'nullable|in:Full-Time,Part-Time,Contractual,Intern,Probation,Freelance',
            'joining_date' => 'required|date',
            'confirmation_date' => 'nullable|date',
            'probation_end_date' => 'nullable|date',
            'last_working_day' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'status' => 'nullable|in:Active,Inactive,On Leave,Suspended,Terminated,Resigned,Retired',
            'portal_active' => 'nullable|boolean',
            'created_by' => 'nullable|integer|exists:employees,id',
        ];
    }
}
