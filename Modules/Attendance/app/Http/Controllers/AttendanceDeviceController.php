<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Attendance\Http\Requests\StoreAttendanceDeviceRequest;
use Modules\Attendance\Http\Requests\UpdateAttendanceDeviceRequest;
use Modules\Attendance\Models\AttendanceDevice;
use Modules\Attendance\Services\AttendanceDeviceService;
use Modules\Branch\Models\Branch;

class AttendanceDeviceController extends Controller
{
    protected AttendanceDeviceService $attendanceDeviceService;

    public function __construct(AttendanceDeviceService $attendanceDeviceService)
    {
        $this->attendanceDeviceService = $attendanceDeviceService;
    }

    public function index(Request $request)
    {
        $branches = Branch::all();
        $devices = AttendanceDevice::all();

        return view('attendance::device.index', compact('branches', 'devices'));
    }

    public function dataTable(Request $request)
    {
        return $this->attendanceDeviceService->getAttendanceDeviceDataTable($request);
    }

    public function store(StoreAttendanceDeviceRequest $request)
    {
        $result = $this->attendanceDeviceService->saveAttendanceDevice($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->attendanceDeviceService->getAttendanceDeviceById($id);

        return response()->json($result);
    }

    public function update(UpdateAttendanceDeviceRequest $request, $id)
    {
        $data = $request->validated();
        $data['attendance_device_id'] = $id;

        $result = $this->attendanceDeviceService->saveAttendanceDevice($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->attendanceDeviceService->deleteAttendanceDevice($id);

        return response()->json($result);
    }
}
