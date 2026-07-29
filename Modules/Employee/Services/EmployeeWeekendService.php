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
    public function storeWeekend(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $weekendDays = $data['weekend_days'] ?? [];
                $weekendDays = array_map('intval', $weekendDays);

                EmployeeWeekend::updateOrCreate(
                    ['employee_id' => $data['employee_id']],
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
                'message' => 'Error saving weekend: '.$e->getMessage(),
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
