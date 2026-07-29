<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'enable_overtime' => 'boolean',
            'overtime_rate_per_hour' => 'nullable|numeric|min:0',
            'overtime_multiplier' => 'nullable|numeric|min:1',
            'enable_late_deduction' => 'boolean',
            'late_deduction_type' => 'required|in:none,per_minute,half_day,full_day',
            'late_deduction_per_minute' => 'nullable|numeric|min:0',
            'late_deduction_fixed' => 'nullable|numeric|min:0',
            'late_grace_minutes' => 'nullable|integer|min:0',
            'enable_half_day_deduction' => 'boolean',
            'half_day_deduction_percent' => 'nullable|numeric|min:0|max:100',
            'enable_absent_deduction' => 'boolean',
            'absent_deduction_days' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'late_deduction_type.required' => 'Late deduction type is required.',
            'late_deduction_type.in' => 'Late deduction type must be: none, per_minute, half_day, or full_day.',
            'half_day_deduction_percent.max' => 'Half day deduction percent cannot exceed 100.',
        ];
    }
}
