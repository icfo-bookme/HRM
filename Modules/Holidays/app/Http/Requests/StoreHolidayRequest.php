<?php

namespace Modules\Holidays\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:300',
            'holiday_date'    => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:holiday_date',
            'holiday_type'    => 'required|in:Public,Government,Company,Optional,Religious,Festival',
            'applicable_to'   => 'required|in:All,Specific,Branch,Department',
            'is_recurring'    => 'nullable|boolean',
            'yearly_recurring'=> 'nullable|boolean',
            'description'     => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Holiday name is required.',
            'name.max'                  => 'Holiday name cannot exceed 300 characters.',
            'holiday_date.required'     => 'Holiday date is required.',
            'holiday_date.date'         => 'Please provide a valid holiday date.',
            'end_date.date'             => 'Please provide a valid end date.',
            'end_date.after_or_equal'   => 'End date must be on or after the holiday date.',
            'holiday_type.required'     => 'Holiday type is required.',
            'holiday_type.in'           => 'Please select a valid holiday type (Public, Government, Company, Optional, Religious, Festival).',
            'applicable_to.required'    => 'Applicable to field is required.',
            'applicable_to.in'          => 'Please select a valid applicable option (All, Specific, Branch, Department).',
        ];
    }
}