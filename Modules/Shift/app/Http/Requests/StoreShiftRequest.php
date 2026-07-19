<?php

namespace Modules\Shift\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'nullable|string|max:150',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i',
            'break_minutes'  => 'nullable|integer|min:0|max:1440',
            'grace_in_min'   => 'nullable|integer|min:0|max:240',
            'grace_out_min'  => 'nullable|integer|min:0|max:240',
            'work_hours'     => 'nullable|numeric|min:0|max:99.9',
            'is_night_shift' => 'nullable|boolean',
            'is_flexible'    => 'nullable|boolean',
            'is_active'      => 'nullable|boolean',
            'metadata'       => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [          
            'name.required'       => 'Shift name is required.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must use HH:MM format.',
            'end_time.required'   => 'End time is required.',
            'end_time.date_format' => 'End time must use HH:MM format.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('metadata') && is_string($this->metadata)) {
            $this->merge([
                'metadata' => json_decode($this->metadata, true),
            ]);
        }

        $this->merge([
            'is_night_shift' => $this->boolean('is_night_shift'),
            'is_flexible'    => $this->boolean('is_flexible'),
            'is_active'      => $this->boolean('is_active'),
        ]);
    }
}
