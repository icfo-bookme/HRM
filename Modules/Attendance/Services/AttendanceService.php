<?php

namespace Modules\Attendance\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Attendance\Models\Attendance;
use Modules\Attendance\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Yajra\DataTables\DataTables;
use Exception;

class AttendanceService
{
    public function getAttendanceDataTable(Request $request)
    {
        $query = Attendance::with('employee.personalInfo')
            ->select('attendance.*')
            ->orderByDesc('attendance.created_at');

        collect([
            'employee_id' => 'attendance.employee_id',
            'attendance_status' => 'attendance.attendance_status',
        ])->each(fn($column, $filter) => $request->filled($filter)
            ? $query->where($column, $request->$filter)
            : null);

        if ($request->attendance_date_from) {
            $query->where('attendance.attendance_date', '>=', $request->attendance_date_from);
        }
        if ($request->attendance_date_to) {
            $query->where('attendance.attendance_date', '<=', $request->attendance_date_to);
        }
        if ($request->late_early === 'late') {
            $query->where('attendance.is_late', true)->where('attendance.late_minutes', '>', 0);
        } elseif ($request->late_early === 'early') {
            $query->where('attendance.is_early_out', true)->where('attendance.early_out_minutes', '>', 0);
        } elseif ($request->late_early === 'on_time') {
            $query->where(function ($q) {
                $q->where('attendance.is_late', false)->orWhereNull('attendance.is_late');
            })->where(function ($q) {
                $q->where('attendance.is_early_out', false)->orWhereNull('attendance.is_early_out');
            });
        }

        $statusColors = [
            'Present' => 'success',
            'Absent' => 'danger',
            'Half Day' => 'warning',
            'On Leave' => 'info',
            'Holiday' => 'primary',
            'Weekend' => 'secondary',
        ];
        $approvalColors = [
            'Pending' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger',
        ];

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('employee', function ($a) {
                $name = $a->employee?->full_name ?? 'Unknown';
                $code = $a->employee?->employee_code ?? '-';

                $initial = strtoupper(substr($name, 0, 1));

                return '
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-lg">
                ' . $initial . '
            </div>
            <div>
                <div class="font-medium">' . $name . '</div>
                <div class="text-sm text-gray-500">' . $code . '</div>
            </div>
        </div>
    ';
            })
            ->editColumn('attendance_date', fn($a) => $a->attendance_date->format('d M '))
            ->addColumn('attendance_time', function ($a) {

                $checkIn = $a->check_in_at
                    ? $a->check_in_at->format('h:i A')
                    : 'N/A';

                $checkOut = $a->check_out_at
                    ? $a->check_out_at->format('h:i A')
                    : 'N/A';

                return '
        <div class="space-y-1">
            <div class="flex text-sm items-center gap-2 text-blue-600">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>' . $checkIn . '</span>
            </div>

            <div class="flex items-center gap-2 text-green-600">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>' . $checkOut . '</span>
            </div>
        </div>
    ';
            })
            ->editColumn('attendance_status', function ($a) {
                $color = $statusColors[$a->attendance_status] ?? 'secondary';
                $html = '<span class="badge badge-' . $color . '">' . $a->attendance_status . '</span>';
                if ($a->is_late && $a->late_minutes > 0) {
                    $html .= '<br><span class="inline-flex items-center rounded-md bg-red-600 px-2 py-0.5 text-xs font-medium text-white shadow-sm mt-1">Late</span>';
                }
                if ($a->is_early_out && $a->early_out_minutes > 0) {
                    $html .= '<br><span class="inline-flex items-center rounded-md bg-orange-600 px-2 py-0.5 text-xs font-medium text-white shadow-sm mt-1">Early</span>';
                }
                return $html;
            })
            ->editColumn('approval_status', fn($a) => sprintf(
                '<span class="badge badge-%s">%s</span>',
                $approvalColors[$a->approval_status] ?? 'secondary',
                $a->approval_status
            ))
            ->addColumn('working_hours', function ($a) {
                if ($a->net_working_minutes && $a->net_working_minutes > 0) {
                    $hours = intdiv($a->net_working_minutes, 60);
                    $mins = $a->net_working_minutes % 60;
                    $total = round($a->net_working_minutes / 60, 1);
                    return '<span class="font-medium text-gray-700">' . $hours . 'h ' . $mins . 'm</span>';
                }
                if ($a->working_minutes && $a->working_minutes > 0) {
                    $hours = intdiv($a->working_minutes, 60);
                    $mins = $a->working_minutes % 60;
                    return '<span class="font-medium text-gray-700">' . $hours . 'h ' . $mins . 'm</span>';
                }
                return '<span class="text-gray-400">—</span>';
            })
            ->addColumn('late_early', function ($a) {
                $parts = [];
                if ($a->is_late && $a->late_minutes > 0) {
                    $hours = round($a->late_minutes / 60, 1);
                    $parts[] = '<span class="inline-flex items-center gap-1  rounded-full text-xs font-medium bg-red-100 text-red-700">
                        <i class="fa-solid fa-clock"></i> Late ' . $a->late_minutes . 'm' . ($hours >= 1 ? ' / ' . $hours . 'h' : '') . '
                    </span>';
                }
                if ($a->is_early_out && $a->early_out_minutes > 0) {
                    $hours = round($a->early_out_minutes / 60, 1);
                    $parts[] = '<span class="inline-flex items-center gap-1  rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                        <i class="fa-solid fa-clock"></i> Early ' . $a->early_out_minutes . 'm' . ($hours >= 1 ? ' / ' . $hours . 'h' : '') . '
                    </span>';
                }
                if (empty($parts)) {
                    return '<span class="text-gray-50 bg-green-600 p-1 rounded-xl">Ontime</span>';
                }
                return implode('<br>', $parts);
            })
            ->addColumn('overtime', function ($a) {
                if ($a->overtime_minutes && $a->overtime_minutes > 0) {
                    $hours = intdiv($a->overtime_minutes, 60);
                    $mins = $a->overtime_minutes % 60;
                    $total = round($a->overtime_minutes / 60, 1);
                    return '<span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700">
                        <i class="fa-solid fa-clock"></i> ' . $hours . 'h ' . $mins . 'm' . ($total >= 1 ? ' (' . $total . 'h)' : '') . '
                    </span>';
                }
                return '<span class="text-gray-400">—</span>';
            })
            ->editColumn('source', fn($a) => '<span class="badge badge-info">' . e($a->source) . '</span>')
            ->addColumn('action', fn(Attendance $a) => view('components.action-buttons', [
                'id' => $a->id,
                'edit' => 'attendanceEdit',
                'delete' => 'attendanceDelete',
            ])->render())
            ->rawColumns(['employee', 'attendance_time', 'working_hours', 'late_early', 'overtime', 'attendance_status', 'approval_status', 'source', 'action'])
            ->make(true);
    }

    private function calculateAttendanceMetrics(array $data, ?Employee $employee): array
    {
        $checkIn = !empty($data['check_in_at']) ? Carbon::parse($data['check_in_at']) : null;
        $checkOut = !empty($data['check_out_at']) ? Carbon::parse($data['check_out_at']) : null;
        $shift = $employee?->shift;

        if ($shift && $checkIn) {
            $scheduledIn = Carbon::parse($data['attendance_date'] . ' ' . $shift->start_time);
            $grace = $shift->grace_in_min ?? 0;
            $isLate = $checkIn->gt($scheduledIn->copy()->addMinutes($grace));
            $data['is_late'] = $isLate;
            $data['late_minutes'] = $isLate ? max(0, $scheduledIn->diffInMinutes($checkIn) - $grace) : 0;
        }

        if ($shift && $checkOut) {
            $scheduledOut = Carbon::parse($data['attendance_date'] . ' ' . $shift->end_time);
            $grace = $shift->grace_out_min ?? 0;
            $isEarly = $checkOut->lt($scheduledOut->copy()->subMinutes($grace));
            $data['is_early_out'] = $isEarly;
            $data['early_out_minutes'] = $isEarly ? max(0, $checkOut->diffInMinutes($scheduledOut) - $grace) : 0;
        }

        if ($checkIn && $checkOut) {
            $working = $checkIn->diffInMinutes($checkOut);
            $data['first_in_at'] = $data['check_in_at'];
            $data['last_out_at'] = $data['check_out_at'];
            $data['working_minutes'] = $working;
            $data['net_working_minutes'] = $working - ($data['break_minutes'] ?? 0);
        } elseif ($checkIn) {
            $data['first_in_at'] = $data['check_in_at'];
        } elseif ($checkOut) {
            $data['last_out_at'] = $data['check_out_at'];
        }

        // ====== Overtime Calculation ======
        // If employee has a shift and checked out, calculate overtime
        // Overtime = minutes worked beyond scheduled end_time + grace_out_min
        if ($shift && $checkOut) {
            $scheduledOut = Carbon::parse($data['attendance_date'] . ' ' . $shift->end_time);
            $graceOut = $shift->grace_out_min ?? 0;

            // Overtime threshold = scheduled end time + grace out period
            $overtimeThreshold = $scheduledOut->copy()->addMinutes($graceOut);

            // If check-out is after the overtime threshold
            if ($checkOut->gt($overtimeThreshold)) {
                $overtimeMinutes = $overtimeThreshold->diffInMinutes($checkOut);
                $data['overtime_minutes'] = $overtimeMinutes;
            } else {
                $data['overtime_minutes'] = 0;
            }
        } else {
            $data['overtime_minutes'] = $data['overtime_minutes'] ?? 0;
        }

        return $data;
    }

    private function createLog(int $employeeId, string $datetime, string $type): void
    {
        AttendanceLog::create([
            'employee_id' => $employeeId,
            'punch_datetime' => $datetime,
            'punch_type' => $type,
            'source' => 'Manual',
            'verification_method' => 'Manual',
            'is_processed' => true,
            'processing_date' => now(),
        ]);
    }

    public function saveManualAttendance(array $data, ?int $id = null): array
    {
        try {
            return DB::transaction(function () use ($data, $id) {
                $employee = Employee::with('shift')->find($data['employee_id'] ?? $data['id']);
                $data['shift_id'] = $employee?->shift_id;

                if ($id) {
                    $attendance = Attendance::findOrFail($id);
                    $merged = $this->calculateAttendanceMetrics(
                        array_merge($attendance->toArray(), $data),
                        $employee
                    );
                    $merged['source'] ??= 'Manual';
                    $merged['updated_by'] = auth()->id();
                    $attendance->update($merged);

                    return ['status' => 'success', 'message' => 'Attendance updated successfully.', 'attendance' => $attendance->fresh()->load('employee.personalInfo')];
                }

                $data += [
                    'source' => 'Manual',
                    'approval_status' => 'Pending',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];

                $data = $this->calculateAttendanceMetrics($data, $employee);
                $attendance = Attendance::create($data);

                if (!empty($data['check_in_at'])) {
                    $this->createLog($data['employee_id'], $data['check_in_at'], 'IN');
                }
                if (!empty($data['check_out_at'])) {
                    $this->createLog($data['employee_id'], $data['check_out_at'], 'OUT');
                }

                return ['status' => 'success', 'message' => 'Attendance added successfully.', 'attendance' => $attendance->fresh()->load('employee.personalInfo')];
            });
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Error saving attendance: ' . $e->getMessage(), 'attendance' => null];
        }
    }

    public function getAttendanceById(int $id): array
    {
        try {
            $attendance = Attendance::with('employee.personalInfo')->findOrFail($id)->toArray();

            // Format datetime fields for the edit form
            $timeFormat = 'H:i';
            if (!empty($attendance['check_in_at'])) {
                $attendance['check_in_at_formatted'] = Carbon::parse($attendance['check_in_at'])->format($timeFormat);
            }
            if (!empty($attendance['check_out_at'])) {
                $attendance['check_out_at_formatted'] = Carbon::parse($attendance['check_out_at'])->format($timeFormat);
            }

            return ['status' => 'success', 'attendance' => $attendance];
        } catch (Exception) {
            return ['status' => 'error', 'message' => 'Attendance record not found.', 'attendance' => null];
        }
    }

    public function deleteAttendance(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                Attendance::findOrFail($id)->delete();
                return ['status' => 'success', 'message' => 'Attendance deleted successfully.'];
            });
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Error deleting attendance: ' . $e->getMessage()];
        }
    }
}