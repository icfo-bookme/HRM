<?php

namespace App\Http\Requests\Salary;

use Illuminate\Foundation\Http\FormRequest;

class PreviewPayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'run_month' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'run_month.required' => 'Please select a payroll month.',
            'run_month.date' => 'Invalid date format for payroll month.',
        ];
    }
}
