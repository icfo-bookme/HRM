<?php

namespace App\Http\Requests\Kpi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIndicatorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'indicators' => 'required|array',
            'indicators.*.id' => 'required|exists:kpi_indicators,id',
            'indicators.*.weight_percentage' => 'required|numeric|min:0|max:100',
            'indicators.*.point_per_unit' => 'nullable|numeric',
            'indicators.*.is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'indicators.required' => 'Indicators data is required.',
            'indicators.*.id.required' => 'Indicator ID is required.',
            'indicators.*.id.exists' => 'Invalid indicator found.',
            'indicators.*.weight_percentage.required' => 'Weight percentage is required for each indicator.',
            'indicators.*.weight_percentage.max' => 'Weight percentage cannot exceed 100.',
        ];
    }
}
