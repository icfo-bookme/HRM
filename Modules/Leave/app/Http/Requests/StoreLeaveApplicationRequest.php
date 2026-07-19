<?php

namespace Modules\Leave\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id'             => 'required|integer|exists:employees,id',
            'leave_type_id'           => 'required|integer|exists:leave_types,id',
            'application_no'          => 'nullable|string|max:30|unique:leave_applications,application_no',
            'from_date'               => 'required|date',
            'to_date'                 => 'required|date|after_or_equal:from_date',
            'total_days'              => 'required|numeric|min:0.5|max:999.9',
            'is_half_day'             => 'nullable|boolean',
            'half_day_period'         => 'nullable|in:First Half,Second Half',
            'reason'                  => 'nullable|string|max:2000',
            'professional_email'      => 'nullable|string|max:5000',
            'document_file'           => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'substitute_employee_id'  => 'nullable|integer|exists:employees,id',
            'contact_during_leave'    => 'nullable|string|max:50',
            'status'                  => 'nullable|in:Draft,Pending,Approved,Rejected,Cancelled,Withdrawn',
            'rejection_reason'        => 'nullable|string|max:2000',
            'approved_by'             => 'nullable|integer|exists:employees,id',
            'approved_at'             => 'nullable|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $employeeId = $this->input('employee_id');
            $fromDate = $this->input('from_date');
            $toDate = $this->input('to_date');
            $excludeId = $this->route('leave_application'); // For updates, exclude self

            if ($employeeId && $fromDate && $toDate) {
                // Check for overlapping leave applications
                $overlapping = \Modules\Leave\Models\LeaveApplication::where('employee_id', $employeeId)
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->where(function ($q) use ($fromDate, $toDate) {
                        $q->whereBetween('from_date', [$fromDate, $toDate])
                          ->orWhereBetween('to_date', [$fromDate, $toDate])
                          ->orWhere(function ($q2) use ($fromDate, $toDate) {
                              $q2->where('from_date', '<=', $fromDate)
                                 ->where('to_date', '>=', $toDate);
                          });
                    });

                if ($excludeId) {
                    $overlapping->where('id', '!=', $excludeId);
                }

                if ($overlapping->exists()) {
                    $overlapApp = $overlapping->first();
                    $validator->errors()->add('from_date', 
                        "This employee already has a {$overlapApp->status} leave application (App#{$overlapApp->application_no}) from {$overlapApp->from_date->format('d M Y')} to {$overlapApp->to_date->format('d M Y')} that overlaps with the requested dates.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'employee_id.required'            => 'Employee is required.',
            'employee_id.exists'              => 'Selected employee does not exist.',
            'leave_type_id.required'          => 'Leave type is required.',
            'leave_type_id.exists'            => 'Selected leave type does not exist.',
            'from_date.required'              => 'Start date is required.',
            'to_date.required'                => 'End date is required.',
            'to_date.after_or_equal'          => 'End date must be on or after the start date.',
            'total_days.required'             => 'Total days is required.',
            'total_days.min'                  => 'Minimum leave duration is 0.5 day.',
            'substitute_employee_id.exists'   => 'Selected substitute employee does not exist.',
            'approved_by.exists'              => 'Selected approver does not exist.',
        ];
    }
}