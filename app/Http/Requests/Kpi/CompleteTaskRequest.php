<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class CompleteTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'obtained_score' => 'required|numeric|min:0|max:999999.99',
            'completion_note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'obtained_score.required' => 'Obtained score is required.',
            'obtained_score.numeric' => 'Obtained score must be a number.',
        ];
    }
}
