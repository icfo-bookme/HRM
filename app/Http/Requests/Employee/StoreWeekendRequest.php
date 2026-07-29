<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeekendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'weekend_days' => 'nullable|array',
            'weekend_days.*' => 'integer|between:0,6',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'weekend_days.*.integer' => 'Weekend days must be valid day numbers (0-6).',
            'weekend_days.*.between' => 'Weekend days must be between 0 (Sun) and 6 (Sat).',
        ];
    }
}
