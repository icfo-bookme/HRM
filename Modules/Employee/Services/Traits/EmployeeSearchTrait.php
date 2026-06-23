<?php

namespace Modules\Employee\Services\Traits;

use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;

trait EmployeeSearchTrait
{
    /**
     * Get paginated employees with search functionality
     */
    public function getPaginatedEmployees(Request $request, int $perPage = 12, array $with = ['personalInfo'])
    {
        $search = $request->get('search');

        $employeesQuery = Employee::with($with)
            ->active()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_code', 'like', "%{$search}%")
                      ->orWhereHas('personalInfo', function ($q2) use ($search) {
                          $q2->where('full_name', 'like', "%{$search}%");
                      });
                });
            });

        return $employeesQuery->paginate($perPage)->appends($request->except('page'));
    }

    /**
     * Prepare AJAX response for index page
     */
    public function prepareAjaxResponse(Request $request, string $viewPath, int $perPage = 12): array
    {
        $search = $request->get('search');
        $employees = $this->getPaginatedEmployees($request, $perPage);

        return [
            'html' => view($viewPath, compact('employees'))->render(),
            'pagination' => view('employee::shared.pagination', compact('employees'))->render(),
            'search' => $search,
        ];
    }
}