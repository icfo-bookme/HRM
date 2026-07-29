<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDependentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dependents' => ['nullable', 'array'],
            'dependents.*.id' => ['nullable', 'exists:employee_dependents,id'],
            'dependents.*.full_name' => ['required', 'string', 'max:255'],
            'dependents.*.relation' => ['required', 'string', 'max:100'],
            'dependents.*.date_of_birth' => ['nullable', 'date'],
            'dependents.*.nid_number' => ['nullable', 'string', 'max:50'],
            'dependents.*.phone' => ['nullable', 'string', 'max:20'],
            'dependents.*.email' => ['nullable', 'email', 'max:200'],
            'dependents.*.occupation' => ['nullable', 'string', 'max:200'],
            'dependents.*.is_nominee' => ['nullable', 'boolean'],
            'dependents.*.nominee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'dependents.*.priority_order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'dependents.*.full_name.required' => 'Full name is required for each dependent.',
            'dependents.*.relation.required' => 'Relation is required for each dependent.',
        ];
    }
}
