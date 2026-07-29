<?php

namespace App\Http\Requests\Holidays;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:300',
            'holiday_type' => 'required|string',
            'applicable_to' => 'required|string',
            'selected_dates' => 'required|string',
            'description' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'yearly_recurring' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Holiday name is required.',
            'holiday_type.required' => 'Holiday type is required.',
            'applicable_to.required' => 'Applicable to field is required.',
            'selected_dates.required' => 'Please select at least one date.',
        ];
    }
}
