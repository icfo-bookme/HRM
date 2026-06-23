<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepEightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->boolean('skip')) {
            return [];
        }

        return [
            'effective_date' => 'nullable|date',
            'change_type' => 'nullable|in:Joining,Promotion,Demotion,Transfer,Designation Change,Grade Change,Salary Revision,Confirmation,Termination,Resignation,Retirement,Rehired',
            'from_branch_id' => 'nullable|integer|exists:branches,id',
            'to_branch_id' => 'nullable|integer|exists:branches,id',
            'from_dept_id' => 'nullable|integer|exists:departments,id',
            'to_dept_id' => 'nullable|integer|exists:departments,id',
            'from_desig_id' => 'nullable|integer|exists:designations,id',
            'to_desig_id' => 'nullable|integer|exists:designations,id',
            'from_grade_id' => 'nullable|integer|exists:salary_grades,id',
            'to_grade_id' => 'nullable|integer|exists:salary_grades,id',
            'from_salary' => 'nullable|numeric|min:0',
            'to_salary' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
            'remarks' => 'nullable|string',
            'approved_by' => 'nullable|integer|exists:employees,id',
        ];
    }
}