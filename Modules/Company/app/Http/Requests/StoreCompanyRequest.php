<?php

namespace Modules\Company\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:300',
            'legal_name' => 'nullable|string|max:300',
            'trade_license' => 'nullable|string|max:100',
            'bin_number' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:50',
            'industry' => 'nullable|string|max:150',
            'founded_year' => 'nullable|integer|min:1800|max:2100',
            'logo_path' => 'nullable|string|max:500',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150|unique:companies,email',
            'website' => 'nullable|url|max:200',
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'fiscal_year_start' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|array',
        ];
    }
}
