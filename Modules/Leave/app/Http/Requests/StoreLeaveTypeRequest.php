<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150|unique:leave_types,name',
            'description' => 'nullable|string',
            'days_per_year' => 'required|numeric|min:0|max:999.9',
            'is_paid' => 'boolean',
            'is_half_day_allowed' => 'boolean',
            'carry_forward' => 'boolean',
            'max_carry_days' => 'required_if:carry_forward,true|numeric|min:0|max:999.9',
            'max_consecutive_days' => 'integer|min:0',
            'requires_document' => 'boolean',
            'min_days_notice' => 'integer|min:0',
            'applicable_gender' => 'in:All,Male,Female',
            'color_code' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'max_carry_days.required_if' => 'Max carry days is required when carry forward is enabled.',
        ];
    }
}