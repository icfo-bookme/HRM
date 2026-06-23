<?php

namespace Modules\Holidays\Services;

use Modules\Holidays\Models\HolidayAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // If an ID is provided (update), fetch the existing assignment combo first
            if (!empty($data['id'])) {
                $existing = HolidayAssignment::find($data['id']);
                if ($existing) {
                    $data['holiday_id'] = $data['holiday_id'] ?? $existing->holiday_id;
                    $data['branch_id'] = $data['branch_id'] ?? $existing->branch_id;
                }
            }

            $holidayId = $data['holiday_id'];
            $branchId = $data['branch_id'] ?? null;
            $departmentIds = $data['department_ids'] ?? [];

            // Remove all existing assignments for this holiday+branch combination
            HolidayAssignment::where('holiday_id', $holidayId)
                ->where('branch_id', $branchId)
                ->delete();

            // If no specific departments selected, create a single assignment with null department
            if (empty($departmentIds)) {
                HolidayAssignment::create([
                    'holiday_id' => $holidayId,
                    'branch_id' => $branchId,
                    'department_id' => null,
                ]);

                return [
                    'status' => true,
                    'message' => 'Holiday assigned successfully (all departments).',
                    'data' => ['holiday_id' => $holidayId, 'branch_id' => $branchId]
                ];
            }

            // Assign to multiple departments
            foreach ($departmentIds as $deptId) {
                HolidayAssignment::create([
                    'holiday_id' => $holidayId,
                    'branch_id' => $branchId,
                    'department_id' => $deptId,
                ]);
            }

            return [
                'status' => true,
                'message' => 'Holiday assigned to ' . count($departmentIds) . ' department(s) successfully.',
                'data' => ['holiday_id' => $holidayId, 'branch_id' => $branchId]
            ];
        });
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

        // Delete all rows for this holiday+branch combination
        HolidayAssignment::where('holiday_id', $assignment->holiday_id)
            ->where('branch_id', $assignment->branch_id)
            ->delete();

        return [
            'status' => true,
            'message' => 'Holiday assignment deleted successfully.'
        ];
    }
}