<?php

namespace Modules\Attendance\Services;

use Modules\Attendance\Models\Attendance;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Get attendance data for DataTable
     */
    public function getAttendanceDataTable($request)
    {
        $query = Attendance::with('employee')->select('attendances.*');

        // Filters
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->attendance_status) {
            $query->where('status', $request->attendance_status);
        }
        if ($request->attendance_date_from) {
            $query->whereDate('attendance_date', '>=', $request->attendance_date_from);
        }
        if ($request->attendance_date_to) {
            $query->whereDate('attendance_date', '<=', $request->attendance_date_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employee', fn($row) => $row->employee?->full_name ?? 'N/A')
            ->editColumn('attendance_date', fn($row) => Carbon::parse($row->attendance_date)->format('d M, Y'))
            ->editColumn('check_in_at', fn($row) => $row->check_in_at ? Carbon::parse($row->check_in_at)->format('h:i A') : '--:--')
            ->editColumn('check_out_at', fn($row) => $row->check_out_at ? Carbon::parse($row->check_out_at)->format('h:i A') : '--:--')
            ->addColumn('attendance_status', function($row) {
                $colors = [
                    'Present' => 'bg-green-100 text-green-700',
                    'Absent' => 'bg-red-100 text-red-700',
                    'Half Day' => 'bg-yellow-100 text-yellow-700',
                ];
                $class = $colors[$row->status] ?? 'bg-gray-100 text-gray-700';
                return '<span class="px-2 py-1 rounded text-xs font-medium '.$class.'">'.$row->status.'</span>';
            })
            ->addColumn('approval_status', fn($row) => '<span class="text-xs text-gray-500 italic">Auto Approved</span>')
            ->addColumn('source', fn($row) => '<span class="text-xs text-gray-400">Manual</span>')
            ->addColumn('action', function($row) {
                return '
                    <div class="flex gap-2">
                        <button onclick="attendanceEdit('.$row->id.')" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button onclick="attendanceDelete('.$row->id.')" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                    </div>';
            })
            ->rawColumns(['attendance_status', 'approval_status', 'source', 'action'])
            ->make(true);
    }

    /**
     * Get single attendance by ID
     */
    public function getAttendanceById($id)
    {
        return Attendance::find($id);
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance($id)
    {
        $attendance = Attendance::find($id);
        if ($attendance) {
            return $attendance->delete();
        }
        return false;
    }

    /**
     * Store or update employee attendance.
     */
    public function storeAttendance(array $data)
    {
        $id = $data['id'] ?? null;

        $attributes = $id ? ['id' => $id] : [
            'employee_id' => $data['employee_id'],
            'attendance_date' => $data['attendance_date'],
        ];

        return Attendance::updateOrCreate($attributes, [
            'employee_id' => $data['employee_id'],
            'attendance_date' => $data['attendance_date'],
            'check_in_at' => $data['check_in_at'] ?? null,
            'check_out_at' => $data['check_out_at'] ?? null,
            'status' => $data['attendance_status'],
            'remarks' => $data['remarks'] ?? null,
        ]);
    }
}