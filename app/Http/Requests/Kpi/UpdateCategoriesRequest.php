<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:kpi_categories,id',
            'categories.*.weight_percentage' => 'required|numeric|min:0|max:100',
            'categories.*.is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Categories data is required.',
            'categories.*.id.required' => 'Category ID is required.',
            'categories.*.id.exists' => 'Invalid category found.',
            'categories.*.weight_percentage.required' => 'Weight percentage is required for each category.',
            'categories.*.weight_percentage.max' => 'Weight percentage cannot exceed 100.',
        ];
    }
}
