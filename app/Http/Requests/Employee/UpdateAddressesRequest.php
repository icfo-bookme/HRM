<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'addresses' => ['nullable', 'array'],
            'addresses.*.id' => ['nullable', 'exists:employee_addresses,id'],
            'addresses.*.address_type' => ['required', 'string', 'in:present,permanent'],
            'addresses.*.house_no' => ['nullable', 'string', 'max:100'],
            'addresses.*.road_no' => ['nullable', 'string', 'max:100'],
            'addresses.*.road_name' => ['nullable', 'string', 'max:255'],
            'addresses.*.village' => ['nullable', 'string', 'max:200'],
            'addresses.*.area' => ['nullable', 'string', 'max:200'],
            'addresses.*.post_office' => ['nullable', 'string', 'max:200'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:20'],
            'addresses.*.city' => ['nullable', 'string', 'max:200'],
            'addresses.*.upazila' => ['nullable', 'string', 'max:200'],
            'addresses.*.district' => ['nullable', 'string', 'max:200'],
            'addresses.*.division' => ['nullable', 'string', 'max:200'],
            'addresses.*.state' => ['nullable', 'string', 'max:200'],
            'addresses.*.country' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'addresses.*.address_type.required' => 'Address type is required for each address.',
            'addresses.*.address_type.in' => 'Address type must be present or permanent.',
        ];
    }
}
