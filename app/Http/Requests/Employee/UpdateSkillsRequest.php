<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['nullable', 'exists:employee_skills,id'],
            'skills.*.category_id' => ['nullable', 'exists:skill_categories,id'],
            'skills.*.skill_name' => ['required', 'string', 'max:200'],
            'skills.*.description' => ['nullable', 'string'],
            'skills.*.proficiency' => ['nullable', 'string', 'max:50'],
            'skills.*.years_of_experience' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'skills.*.last_used_date' => ['nullable', 'date'],
            'skills.*.certification' => ['nullable', 'string', 'max:255'],
            'skills.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'skills.*.skill_name.required' => 'Skill name is required for each entry.',
        ];
    }
}
