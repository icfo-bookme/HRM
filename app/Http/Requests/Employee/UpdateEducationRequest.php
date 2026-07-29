<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'educations' => ['nullable', 'array'],
            'educations.*.id' => ['nullable', 'exists:employee_education,id'],
            'educations.*.degree' => ['required', 'string', 'max:255'],
            'educations.*.major_subject' => ['nullable', 'string', 'max:255'],
            'educations.*.institution' => ['nullable', 'string', 'max:255'],
            'educations.*.board_university' => ['nullable', 'string', 'max:255'],
            'educations.*.passing_year' => ['nullable', 'string', 'max:20'],
            'educations.*.result_type' => ['nullable', 'string', 'max:50'],
            'educations.*.result_value' => ['nullable', 'string', 'max:50'],
            'educations.*.duration_from' => ['nullable', 'date'],
            'educations.*.duration_to' => ['nullable', 'date'],
            'educations.*.country' => ['nullable', 'string', 'max:100'],
            'educations.*.is_highest' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'educations.*.degree.required' => 'Degree name is required for each education entry.',
        ];
    }
}
