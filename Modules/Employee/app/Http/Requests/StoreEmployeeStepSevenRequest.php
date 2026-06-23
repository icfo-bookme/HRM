<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepSevenRequest extends FormRequest
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
            'experiences' => 'required|array|min:1',
            'experiences.*.company_name' => 'required|string|max:300',
            'experiences.*.designation' => 'nullable|string|max:200',
            'experiences.*.department' => 'nullable|string|max:200',
            'experiences.*.from_date' => 'nullable|date',
            'experiences.*.to_date' => 'nullable|date',
            'experiences.*.is_current' => 'nullable|boolean',
            'experiences.*.responsibilities' => 'nullable|string',
            'experiences.*.achievements' => 'nullable|string',
            'experiences.*.reason_for_leaving' => 'nullable|string|max:300',
            'experiences.*.salary_scale' => 'nullable|string|max:100',
            'experiences.*.reference_name' => 'nullable|string|max:200',
            'experiences.*.reference_phone' => 'nullable|string|max:20',
            'experiences.*.reference_email' => 'nullable|email|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'experiences.required' => 'Please add at least one experience entry.',
            'experiences.*.company_name.required' => 'Company name is required for each experience entry.',
        ];
    }
}