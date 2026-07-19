<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Prepare the data for validation - convert empty strings to null
     * so nullable rules work properly.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'check_in_at' => $this->input('check_in_at') ?: null,
            'check_out_at' => $this->input('check_out_at') ?: null,
            'remarks' => $this->input('remarks') ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer',
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'attendance_status' => 'nullable|in:Present,Absent,Half Day,On Leave,Holiday,Weekend',
            'check_in_at' => 'nullable|string',
            'check_out_at' => 'nullable|string',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_status.in' => 'Please select a valid attendance status.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}