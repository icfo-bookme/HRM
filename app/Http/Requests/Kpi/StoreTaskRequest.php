<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_score' => 'required|numeric|min:0.1|max:999999.99',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'deadline' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'title.required' => 'Task title is required.',
            'target_score.required' => 'Target score is required.',
            'priority.required' => 'Priority level is required.',
            'priority.in' => 'Priority must be Low, Medium, High, or Critical.',
            'deadline.after_or_equal' => 'Deadline must be today or a future date.',
        ];
    }
}
