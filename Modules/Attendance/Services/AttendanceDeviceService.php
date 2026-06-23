<?php

namespace Modules\Attendance\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Attendance\Models\AttendanceDevice;
use Yajra\DataTables\DataTables;

class AttendanceDeviceService
{
    public function getAttendanceDeviceDataTable(Request $request)
    {
        $query = AttendanceDevice::with('branch')
            ->select(
                'attendance_devices.id',
                'attendance_devices.branch_id',
                'attendance_devices.device_name',
                'attendance_devices.device_code',
                'attendance_devices.device_type',
                'attendance_devices.serial_number',
                'attendance_devices.ip_address',
                'attendance_devices.port',
                'attendance_devices.communication_type',
                'attendance_devices.sync_status',
                'attendance_devices.is_active',
                'attendance_devices.last_sync_at',
                'attendance_devices.created_at',
                'attendance_devices.updated_at'
            )
            ->orderByDesc('attendance_devices.created_at');

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('attendance_devices.is_active', $request->is_active);
        }

        if ($request->sync_status !== null && $request->sync_status !== '') {
            $query->where('attendance_devices.sync_status', $request->sync_status);
        }

        if ($request->device_type !== null && $request->device_type !== '') {
            $query->where('attendance_devices.device_type', $request->device_type);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function ($device) {
                return statusBadge($device->is_active);
            })
            ->editColumn('sync_status', function ($device) {
                $colors = [
                    'Online' => 'success',
                    'Offline' => 'danger',
                    'Syncing' => 'warning',
                    'Error' => 'danger',
                ];
                $color = $colors[$device->sync_status] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . $device->sync_status . '</span>';
            })
            ->editColumn('created_at', function (AttendanceDevice $device) {
                return $device->created_at->format('d M Y H:i');
            })
            ->editColumn('last_sync_at', function (AttendanceDevice $device) {
                return $device->last_sync_at ? $device->last_sync_at->format('d M Y H:i') : 'N/A';
            })
            ->addColumn('action', function (AttendanceDevice $device) {
                return view('components.action-buttons', [
                    'id'     => $device->id,
                    'edit'   => 'attendanceDeviceEdit',
                    'delete' => 'attendanceDeviceDelete',
                ])->render();
            })
            ->rawColumns(['is_active', 'sync_status', 'action'])
            ->make(true);
    }

    public function saveAttendanceDevice(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $deviceId = $data['attendance_device_id'] ?? null;

                if ($deviceId) {
                    // Update existing device
                    $device = AttendanceDevice::findOrFail($deviceId);
                    $device->update($data);
                    $message = 'Attendance device updated successfully.';
                    $status  = 'success';
                } else {
                    // Create new device
                    $device = AttendanceDevice::create($data);
                    $message    = 'Attendance device created successfully.';
                    $status     = 'success';
                }

                return [
                    'status'     => $status,
                    'message'    => $message,
                    'device' => $device->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'     => 'error',
                'message'    => 'Error saving attendance device: ' . $e->getMessage(),
                'device' => null,
            ];
        }
    }

    public function getAttendanceDeviceById(int $id): array
    {
        try {
            $device = AttendanceDevice::findOrFail($id);
            return [
                'status'     => 'success',
                'device' => $device,
            ];
        } catch (\Exception $e) {
            return [
                'status'     => 'error',
                'message'    => 'Attendance device not found.',
                'device' => null,
            ];
        }
    }

    public function deleteAttendanceDevice(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $device = AttendanceDevice::findOrFail($id);
                $device->update(['deleted_at' => now()]);

                return [
                    'status'  => 'success',
                    'message' => 'Attendance device deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting attendance device: ' . $e->getMessage(),
            ];
        }
    }
}