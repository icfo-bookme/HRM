<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepSixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->boolean('skip')) {
            return [];
        }

        return [
            'educations' => 'required|array|min:1',
            'educations.*.degree' => 'required|string|max:200',
            'educations.*.major_subject' => 'nullable|string|max:200',
            'educations.*.institution' => 'nullable|string|max:300',
            'educations.*.board_university' => 'nullable|string|max:300',
            'educations.*.passing_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'educations.*.result_type' => 'nullable|in:CGPA,Percentage,Grade,Division',
            'educations.*.result_value' => 'nullable|string|max:50',
            'educations.*.duration_from' => 'nullable|date',
            'educations.*.duration_to' => 'nullable|date',
            'educations.*.country' => 'nullable|string|max:100',
            'educations.*.is_highest' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'educations.required' => 'Please add at least one education entry.',
            'educations.*.degree.required' => 'Degree is required for each education entry.',
        ];
    }
}