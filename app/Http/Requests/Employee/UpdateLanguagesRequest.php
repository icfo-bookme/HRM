<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'languages' => ['nullable', 'array'],
            'languages.*.id' => ['nullable', 'exists:employee_languages,id'],
            'languages.*.language_name' => ['required', 'string', 'max:100'],
            'languages.*.proficiency' => ['nullable', 'string', 'max:50'],
            'languages.*.can_read' => ['nullable', 'boolean'],
            'languages.*.can_write' => ['nullable', 'boolean'],
            'languages.*.can_speak' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'languages.*.language_name.required' => 'Language name is required for each entry.',
        ];
    }
}
