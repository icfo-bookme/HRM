<?php

namespace Modules\Holidays\Services;

use Modules\Holidays\Models\HolidayAssignment;
use Modules\Holidays\Models\Holiday;
use Modules\Attendance\Models\Attendance;
use Modules\Employee\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class HolidayAssignmentService
{
    public function getAssignmentDataTable(Request $request)
    {
        $query = HolidayAssignment::with(['holiday', 'branch'])
            ->select(
                'holiday_id',
                'branch_id',
                DB::raw('GROUP_CONCAT(department_id ORDER BY department_id SEPARATOR \',\') as department_ids'),
                DB::raw('MIN(id) as id'),
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('MIN(updated_at) as updated_at')
            )
            ->groupBy('holiday_id', 'branch_id');

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return view('components.action-buttons', [
                    'id' => $row->id,
                    'edit' => 'holidayAssignmentEdit',
                    'delete' => 'holidayAssignmentDelete'
                ])->render();
            })
            ->editColumn('holiday_id', function ($row) {
                return $row->holiday ? $row->holiday->name : '';
            })
            ->editColumn('branch_id', function ($row) {
                return $row->branch ? $row->branch->name : '<span class="text-slate-400">All</span>';
            })
            ->editColumn('department_ids', function ($row) {
                if (empty($row->department_ids)) {
                    return '<span class="text-slate-400">All</span>';
                }

                $ids = explode(',', $row->department_ids);
                $names = \Modules\Department\Models\Department::whereIn('id', $ids)
                    ->pluck('name')
                    ->toArray();

                return implode(', ', $names);
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y H:i') : '';
            })
            ->rawColumns(['action', 'branch_id', 'department_ids'])
            ->make(true);
    }

    public function saveAssignment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $holidayId = $data['holiday_id'];
            $branchId = $data['branch_id'] ?? null;
            $newDepartmentIds = $data['department_ids'] ?? [];

            // If an ID is provided (update), fetch the OLD department IDs before deleting
            if (!empty($data['id'])) {
                $existing = HolidayAssignment::find($data['id']);
                if ($existing) {
                    $holidayId = $data['holiday_id'] ?? $existing->holiday_id;
                    $branchId = $data['branch_id'] ?? $existing->branch_id;

                    // Get OLD department IDs from existing assignments for this holiday+branch combo
                    $oldDepartmentIds = HolidayAssignment::where('holiday_id', $holidayId)
                        ->where('branch_id', $branchId)
                        ->whereNotNull('department_id')
                        ->pluck('department_id')
                        ->toArray();

                    // Check if old assignment was for ALL departments (no specific dept selected)
                    $wasAllDepartments = HolidayAssignment::where('holiday_id', $holidayId)
                        ->where('branch_id', $branchId)
                        ->whereNull('department_id')
                        ->exists();

                    // Remove OLD attendance records using the OLD department selection
                    $this->removeHolidayAttendance($holidayId, $branchId, $wasAllDepartments ? null : $oldDepartmentIds);
                }
            } else {
                // For new assignments, remove any existing auto-created attendance for the new selection
                // (in case this holiday was previously assigned and then unassigned)
                $this->removeHolidayAttendance($holidayId, $branchId, $newDepartmentIds);
            }

            // Remove all existing assignments for this holiday+branch combination
            HolidayAssignment::where('holiday_id', $holidayId)
                ->where('branch_id', $branchId)
                ->delete();

            // If no specific departments selected, create a single assignment with null department (all departments)
            if (empty($newDepartmentIds)) {
                HolidayAssignment::create([
                    'holiday_id' => $holidayId,
                    'branch_id' => $branchId,
                    'department_id' => null,
                ]);

                // Create Holiday attendance for all employees in this branch
                $this->createHolidayAttendance($holidayId, $branchId, null);

                return [
                    'status' => true,
                    'message' => 'Holiday assigned successfully (all departments).',
                    'data' => ['holiday_id' => $holidayId, 'branch_id' => $branchId]
                ];
            }

            // Assign to multiple departments
            foreach ($newDepartmentIds as $deptId) {
                HolidayAssignment::create([
                    'holiday_id' => $holidayId,
                    'branch_id' => $branchId,
                    'department_id' => $deptId,
                ]);
            }

            // Create Holiday attendance for employees in selected departments
            $this->createHolidayAttendance($holidayId, $branchId, $newDepartmentIds);

            return [
                'status' => true,
                'message' => 'Holiday assigned to ' . count($newDepartmentIds) . ' department(s) successfully.',
                'data' => ['holiday_id' => $holidayId, 'branch_id' => $branchId]
            ];
        });
    }

    /**
     * Create Holiday attendance records for employees under this holiday assignment.
     */
    private function createHolidayAttendance(int $holidayId, ?int $branchId, ?array $departmentIds): void
    {
        $holiday = Holiday::find($holidayId);
        if (!$holiday || !$holiday->holiday_date) {
            Log::warning('Holiday not found or missing holiday_date for attendance creation.', [
                'holiday_id' => $holidayId
            ]);
            return;
        }

        // Determine the range of dates for this holiday
        $startDate = $holiday->holiday_date->format('Y-m-d');
        $endDate = $holiday->end_date ? $holiday->end_date->format('Y-m-d') : $startDate;

        // Build employee query based on branch/department filter
        $employeeQuery = Employee::where('status', 'Active')
            ->whereNull('deleted_at');

        if ($branchId) {
            $employeeQuery->where('branch_id', $branchId);
        }

        if (!empty($departmentIds)) {
            $employeeQuery->whereIn('department_id', $departmentIds);
        }

        $employees = $employeeQuery->get(['id', 'shift_id']);

        if ($employees->isEmpty()) {
            Log::info('No active employees found for holiday attendance creation.', [
                'holiday_id' => $holidayId,
                'branch_id' => $branchId,
                'department_ids' => $departmentIds
            ]);
            return;
        }

        // Convert dates to Carbon for iteration
        $currentDate = \Carbon\Carbon::parse($startDate);
        $endDateCarbon = \Carbon\Carbon::parse($endDate);

        foreach ($employees as $employee) {
            $dateCursor = $currentDate->copy();

            while ($dateCursor->lte($endDateCarbon)) {
                $attendanceDate = $dateCursor->format('Y-m-d');

                try {
                    // Use updateOrCreate to handle the unique constraint on (employee_id, attendance_date)
                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'attendance_date' => $attendanceDate,
                        ],
                        [
                            'shift_id' => $employee->shift_id,
                            'attendance_status' => 'Holiday',
                            'approval_status' => 'Approved',
                            'source' => 'Leave Auto',
                            'is_absent' => false,
                            'is_late' => false,
                            'is_early_out' => false,
                            'is_holiday_work' => false,
                            'break_minutes' => 0,
                            'working_minutes' => 0,
                            'net_working_minutes' => 0,
                            'late_minutes' => 0,
                            'early_out_minutes' => 0,
                            'overtime_minutes' => 0,
                            'first_in_at' => null,
                            'last_out_at' => null,
                            'check_in_at' => null,
                            'check_out_at' => null,
                            'remarks' => 'Holiday: ' . $holiday->name,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to create holiday attendance record.', [
                        'employee_id' => $employee->id,
                        'attendance_date' => $attendanceDate,
                        'holiday_id' => $holidayId,
                        'error' => $e->getMessage(),
                    ]);
                }

                $dateCursor->addDay();
            }
        }

        Log::info('Holiday attendance records created/updated.', [
            'holiday_id' => $holidayId,
            'holiday_name' => $holiday->name,
            'employee_count' => $employees->count(),
            'date_range' => $startDate . ' to ' . $endDate,
            'branch_id' => $branchId,
            'department_ids' => $departmentIds,
        ]);
    }

    /**
     * Remove previously auto-created Holiday attendance records for this assignment.
     */
    private function removeHolidayAttendance(int $holidayId, ?int $branchId, ?array $departmentIds): void
    {
        $holiday = Holiday::find($holidayId);
        if (!$holiday || !$holiday->holiday_date) {
            return;
        }

        $startDate = $holiday->holiday_date->format('Y-m-d');
        $endDate = $holiday->end_date ? $holiday->end_date->format('Y-m-d') : $startDate;

        $employeeQuery = Employee::where('status', 'Active')
            ->whereNull('deleted_at');

        if ($branchId) {
            $employeeQuery->where('branch_id', $branchId);
        }

        if (!empty($departmentIds)) {
            $employeeQuery->whereIn('department_id', $departmentIds);
        }

        $employeeIds = $employeeQuery->pluck('id');

        if ($employeeIds->isEmpty()) {
            return;
        }

        // Remove attendance records for these employees within the holiday date range
        Attendance::whereIn('employee_id', $employeeIds)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('attendance_status', 'Holiday')
            ->where('source', 'Leave Auto')
            ->delete();

        Log::info('Removed previous holiday attendance records.', [
            'holiday_id' => $holidayId,
            'employee_count' => $employeeIds->count(),
            'date_range' => $startDate . ' to ' . $endDate,
        ]);
    }

    public function getAssignmentById($id)
    {
        $assignment = HolidayAssignment::with(['holiday', 'branch'])->find($id);

        if (!$assignment) {
            return [
                'status' => false,
                'message' => 'Holiday assignment not found!'
            ];
        }

        // Get all department IDs for this holiday+branch combo
        $allDeptIds = HolidayAssignment::where('holiday_id', $assignment->holiday_id)
            ->where('branch_id', $assignment->branch_id)
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->toArray();

        $data = $assignment->toArray();
        $data['all_department_ids'] = $allDeptIds;

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function deleteAssignment($id)
    {
        $assignment = HolidayAssignment::find($id);

        if (!$assignment) {
            return [
                'status' => false,
                'message' => 'Holiday assignment not found or already deleted.'
            ];
        }

        // Get info before deleting to use for cleanup
        $holidayId = $assignment->holiday_id;
        $branchId = $assignment->branch_id;

        // Get all department IDs for this holiday+branch combo
        $allDeptIds = HolidayAssignment::where('holiday_id', $holidayId)
            ->where('branch_id', $branchId)
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->toArray();

        // Delete all rows for this holiday+branch combination
        HolidayAssignment::where('holiday_id', $assignment->holiday_id)
            ->where('branch_id', $assignment->branch_id)
            ->delete();

        // Also remove the auto-created holiday attendance records
        $this->removeHolidayAttendance($holidayId, $branchId, $allDeptIds);

        return [
            'status' => true,
            'message' => 'Holiday assignment deleted successfully.'
        ];
    }
    
}
