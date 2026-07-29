<?php

namespace Modules\Employee\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\EmployeeAttendanceRule;
use Modules\Employee\Services\Traits\EmployeeSearchTrait;

class EmployeeAttendanceRuleService
{
    use EmployeeSearchTrait;

    public function storeRule(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['enable_overtime'] = $data['enable_overtime'] ?? false;
                $data['enable_late_deduction'] = $data['enable_late_deduction'] ?? false;
                $data['enable_half_day_deduction'] = $data['enable_half_day_deduction'] ?? false;
                $data['enable_absent_deduction'] = $data['enable_absent_deduction'] ?? false;
                $data['created_by'] = auth()->id();
                $data['updated_by'] = auth()->id();

                EmployeeAttendanceRule::updateOrCreate(
                    ['employee_id' => $data['employee_id']],
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
                'message' => 'Error saving rule: '.$e->getMessage(),
            ];
        }
    }

    public function getRuleByEmployeeId(int $employeeId): array
    {
        $rule = EmployeeAttendanceRule::where('employee_id', $employeeId)->first();

        return [
            'status' => 'success',
            'rule' => $rule,
        ];
    }

    public function prepareAjaxResponse(Request $request, int $perPage = 12): array
    {
        $search = $request->get('search');
        $employees = $this->getPaginatedEmployees($request, $perPage);

        return [
            'html' => view('employee::attendance-rules.partials.employee-cards', compact('employees'))->render(),
            'pagination' => view('employee::attendance-rules.partials.pagination', compact('employees'))->render(),
            'search' => $search,
        ];
    }
}
