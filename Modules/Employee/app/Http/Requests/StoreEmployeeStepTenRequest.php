<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepTenRequest extends FormRequest
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
            'category_id' => 'nullable|integer|exists:skill_categories,id',
            'skill_name' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:1000',
            'proficiency' => 'nullable|in:Beginner,Intermediate,Advanced,Expert,Master',
            'years_of_experience' => 'nullable|numeric|min:0|max:99.9',
            'last_used_date' => 'nullable|date',
            'certification' => 'nullable|string|max:300',
            'is_active' => 'nullable|boolean',
        ];
    }
}