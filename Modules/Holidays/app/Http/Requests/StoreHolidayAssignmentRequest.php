<?php

namespace Modules\Holidays\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Holidays\Models\HolidayAssignment;

class StoreHolidayAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'holiday_id'     => 'required|integer|exists:holidays,id',
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

    public function after(): array
    {
        return [
            function ($validator) {
                $holidayId = $this->input('holiday_id');
                $branchId = $this->input('branch_id', null);
                $departmentIds = $this->input('department_ids', []);

                if (empty($departmentIds)) {
                    // Check if already assigned to all (null department)
                    $exists = HolidayAssignment::where('holiday_id', $holidayId)
                        ->where('branch_id', $branchId)
                        ->whereNull('department_id')
                        ->exists();

                    if ($exists) {
                        $validator->errors()->add('holiday_id', 'This holiday is already assigned. Please edit or delete the existing assignment instead.');
                    }
                } else {
                    // Check each department
                    $alreadyAssigned = [];
                    foreach ($departmentIds as $deptId) {
                        $exists = HolidayAssignment::where('holiday_id', $holidayId)
                            ->where('branch_id', $branchId)
                            ->where('department_id', $deptId)
                            ->exists();

                        if ($exists) {
                            $alreadyAssigned[] = $deptId;
                        }
                    }

                    if (!empty($alreadyAssigned)) {
                        $deptNames = \Modules\Department\Models\Department::whereIn('id', $alreadyAssigned)
                            ->pluck('name')
                            ->implode(', ');

                        $validator->errors()->add('department_ids', 
                            'This holiday is already assigned to: ' . $deptNames . '. Please edit or delete the existing assignment(s) instead.');
                    }
                }
            }
        ];
    }
}