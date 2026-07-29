<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role_id' => ['nullable', 'exists:roles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ];

        // Password: required for create, optional for update
        if ($userId) {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already in use.',
            'password.required' => 'Password is required for new users.',
            'password.min' => 'Password must be at least 8 characters.',
            'role_id.exists' => 'Selected role is invalid.',
            'employee_id.exists' => 'Selected employee is invalid.',
        ];
    }
}
