<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'give_behavior' => 'boolean',
            'behavior_score' => 'nullable|numeric|min:0|max:10',
            'behavior_remarks' => 'nullable|string|max:500',
            'give_bonus' => 'boolean',
            'bonus_score' => 'nullable|numeric|min:0|max:10',
            'bonus_remarks' => 'nullable|string|max:500',
            'give_penalty' => 'boolean',
            'penalty_score' => 'nullable|numeric|min:0|max:10',
            'penalty_remarks' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Please select an employee.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'year.required' => 'Year is required.',
            'month.required' => 'Month is required.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
        ];
    }
}
