<?php

namespace Modules\Salary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year_id'   => 'required|integer|exists:fiscal_years,id',
            'run_month' => 'required|date|unique:payroll_runs,run_month',
           'run_label'        => 'nullable|string|max:100|unique:payroll_runs,run_label',
            'run_type'         => 'nullable|in:Regular,Bonus,Advance,Adjustment',
            'total_employees'  => 'nullable|integer|min:0',
            'total_gross'      => 'nullable|numeric|min:0',
            'total_net'        => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'status'           => 'nullable|in:Draft,Processing,Calculated,Reviewed,Approved,Disbursed,Locked,Cancelled',
            'notes'            => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'fiscal_year_id.required'  => 'Fiscal year is required.',
            'fiscal_year_id.exists'    => 'Selected fiscal year does not exist.',
            'run_month.required'       => 'Run month is required.',
            'run_month.unique'   => 'Payroll for this month already exists.',
            'run_month.date'           => 'Run month must be a valid date.',
            'run_type.in'              => 'Run type is invalid.',
            'status.in'                => 'Status is invalid.',
        ];
    }
}