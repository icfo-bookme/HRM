<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeLeaveBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id'     => 'required|integer|exists:employees,id',
            'leave_type_id'   => 'required|integer|exists:leave_types,id',
            'fiscal_year_id'  => 'required|integer',
            'opening_balance' => 'nullable|numeric|min:0|max:99999',
            'earned_days'     => 'nullable|numeric|min:0|max:99999',
            'used_days'       => 'nullable|numeric|min:0|max:99999',
            'encashed_days'   => 'nullable|numeric|min:0|max:99999',
            'lapsed_days'     => 'nullable|numeric|min:0|max:99999',
            'pending_days'    => 'nullable|numeric|min:0|max:99999',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'   => 'Employee is required.',
            'employee_id.exists'     => 'Selected employee does not exist.',
            'leave_type_id.required' => 'Leave type is required.',
            'leave_type_id.exists'   => 'Selected leave type does not exist.',
            'fiscal_year_id.required' => 'Fiscal year is required.',
        ];
    }
}