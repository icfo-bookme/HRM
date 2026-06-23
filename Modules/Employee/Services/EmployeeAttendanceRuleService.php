<?php

namespace Modules\Employee\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeAttendanceRule;
use Modules\Employee\Services\Traits\EmployeeSearchTrait;

class EmployeeAttendanceRuleService
{
    use EmployeeSearchTrait;

    /**
     * Store or update employee attendance rule
     */
    public function storeRule(Request $request): array
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'enable_overtime' => 'boolean',
            'overtime_rate_per_hour' => 'nullable|numeric|min:0',
            'overtime_multiplier' => 'nullable|numeric|min:1',
            'enable_late_deduction' => 'boolean',
            'late_deduction_type' => 'required|in:none,per_minute,half_day,full_day',
            'late_deduction_per_minute' => 'nullable|numeric|min:0',
            'late_deduction_fixed' => 'nullable|numeric|min:0',
            'late_grace_minutes' => 'nullable|integer|min:0',
            'enable_half_day_deduction' => 'boolean',
            'half_day_deduction_percent' => 'nullable|numeric|min:0|max:100',
            'enable_absent_deduction' => 'boolean',
            'absent_deduction_days' => 'nullable|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $data = $request->all();
                $data['enable_overtime'] = $request->boolean('enable_overtime');
                $data['enable_late_deduction'] = $request->boolean('enable_late_deduction');
                $data['enable_half_day_deduction'] = $request->boolean('enable_half_day_deduction');
                $data['enable_absent_deduction'] = $request->boolean('enable_absent_deduction');
                $data['created_by'] = auth()->id();
                $data['updated_by'] = auth()->id();

                EmployeeAttendanceRule::updateOrCreate(
                    ['employee_id' => $request->employee_id],
                    $data
                );

                return [
                    'status' => 'success',
                    'message' => 'Attendance rule updated successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving rule: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get attendance rule by employee ID
     */
    public function getRuleByEmployeeId(int $employeeId): array
    {
        $rule = EmployeeAttendanceRule::where('employee_id', $employeeId)->first();

        return [
            'status' => 'success',
            'rule' => $rule,
        ];
    }

    /**
     * Prepare AJAX response for index page
     */
    public function prepareAjaxResponse(Request $request, int $perPage = 12): array
    {
        $search = $request->get('search');
        $employees = $this->getPaginatedEmployees($request, $perPage);

        return [
            'html' => view('employee::attendance-rules.partials.employee-cards', compact('employees'))->render(),
            'pagination' => view('employee::components.pagination', compact('employees'))->render(),
            'search' => $search,
        ];
    }
}