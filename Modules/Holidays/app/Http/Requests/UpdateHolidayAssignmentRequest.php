<?php

namespace Modules\Holidays\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHolidayAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'holiday_id'     => 'sometimes|required|integer|exists:holidays,id',
            'branch_id'      => 'nullable|integer|exists:branches,id',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'holiday_id.required'  => 'Holiday is required.',
            'holiday_id.exists'    => 'Selected holiday does not exist.',
            'branch_id.exists'     => 'Selected branch does not exist.',
            'department_ids.array' => 'Departments must be an array.',
            'department_ids.*.exists' => 'One or more selected departments do not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('department_ids') && is_string($this->department_ids)) {
            $decoded = json_decode($this->department_ids, true);
            $this->merge([
                'department_ids' => is_array($decoded) ? $decoded : [],
            ]);
        }
    }
}