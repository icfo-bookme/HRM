<?php

namespace Modules\Employee\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeWeekend;
use Modules\Employee\Services\Traits\EmployeeSearchTrait;

class EmployeeWeekendService
{
    use EmployeeSearchTrait;

    /**
     * Store or update employee weekend
     */
    public function storeWeekend(Request $request): array
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'weekend_days' => 'nullable|array',
            'weekend_days.*' => 'integer|between:0,6',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $weekendDays = $request->weekend_days ?? [];
                $weekendDays = array_map('intval', $weekendDays);

                EmployeeWeekend::updateOrCreate(
                    ['employee_id' => $request->employee_id],
                    ['weekend_days' => $weekendDays]
                );

                return [
                    'status' => 'success',
                    'message' => 'Employee weekend updated successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving weekend: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get weekend by employee ID
     */
    public function getWeekendByEmployeeId(int $employeeId): array
    {
        $weekend = EmployeeWeekend::where('employee_id', $employeeId)->first();

        return [
            'status' => 'success',
            'weekend' => $weekend,
        ];
    }

    /**
     * Prepare AJAX response for index page
     */
    public function prepareAjaxResponse(Request $request, int $perPage = 12): array
    {
        $search = $request->get('search');
        $employees = $this->getPaginatedEmployees($request, $perPage, ['personalInfo', 'weekend']);

        return [
            'html' => view('employee::weekends.partials.employee-cards', compact('employees'))->render(),
            'pagination' => view('employee::shared.pagination', compact('employees'))->render(),
            'search' => $search,
        ];
    }
}
