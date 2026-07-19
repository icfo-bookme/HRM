<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('skill_category');

        return [
            'name'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('skill_categories', 'name')->ignore($categoryId),
            ],
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Skill category name is required.',
            'name.unique'   => 'This skill category name already exists.',
        ];
    }
}