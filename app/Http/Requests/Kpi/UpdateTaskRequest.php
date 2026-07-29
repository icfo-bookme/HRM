<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_score' => 'required|numeric|min:0.1|max:999999.99',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:Pending,In Progress',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'target_score.required' => 'Target score is required.',
            'priority.required' => 'Priority level is required.',
            'priority.in' => 'Priority must be Low, Medium, High, or Critical.',
        ];
    }
}
