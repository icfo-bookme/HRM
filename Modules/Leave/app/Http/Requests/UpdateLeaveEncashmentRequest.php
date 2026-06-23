<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveEncashmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $encashmentId = $this->route('leave_encashment');

        return [
            'employee_id'     => 'required|integer|exists:employees,id',
            'leave_type_id'   => 'required|integer|exists:leave_types,id',
            'encashment_date' => 'required|date',
            'days_encashed'   => 'required|numeric|min:0.5|max:999.9',
            'amount_per_day'  => 'nullable|numeric|min:0|max:999999999999.99',
            'total_amount'    => 'nullable|numeric|min:0|max:999999999999.99',
            'payroll_run_id'  => 'nullable|integer',
            'reason'          => 'nullable|string|max:2000',
            'approved_by'     => 'nullable|integer|exists:employees,id',
            'approved_at'     => 'nullable|date',
            'status'          => 'nullable|in:Pending,Approved,Paid',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'     => 'Employee is required.',
            'employee_id.exists'       => 'Selected employee does not exist.',
            'leave_type_id.required'   => 'Leave type is required.',
            'leave_type_id.exists'     => 'Selected leave type does not exist.',
            'encashment_date.required' => 'Encashment date is required.',
            'days_encashed.required'   => 'Days encashed is required.',
            'days_encashed.min'        => 'Minimum encashment is 0.5 day.',
            'approved_by.exists'       => 'Selected approver does not exist.',
        ];
    }
}