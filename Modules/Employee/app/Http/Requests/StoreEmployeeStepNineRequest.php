<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepNineRequest extends FormRequest
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
            'languages' => 'required|array|min:1',
            'languages.*.language_name' => 'required|string|max:50',
            'languages.*.proficiency' => 'nullable|in:Basic,Conversational,Professional,Native',
            'languages.*.can_read' => 'nullable|boolean',
            'languages.*.can_write' => 'nullable|boolean',
            'languages.*.can_speak' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'languages.required' => 'Please add at least one language entry.',
            'languages.*.language_name.required' => 'Language name is required for each entry.',
        ];
    }
}