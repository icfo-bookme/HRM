<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Attendance\Services\AttendanceDeviceService;
use Modules\Attendance\Http\Requests\StoreAttendanceDeviceRequest;
use Modules\Attendance\Http\Requests\UpdateAttendanceDeviceRequest;
use Illuminate\Http\Request;
use Modules\Branch\Models\Branch;
use Modules\Attendance\Models\AttendanceDevice;

class AttendanceDeviceController extends Controller
{
    protected $attendanceDeviceService;

    public function __construct(AttendanceDeviceService $attendanceDeviceService)
    {
        $this->attendanceDeviceService = $attendanceDeviceService;
    }

    /**
     * Display attendance device listing page
     */
    public function index(Request $request)
    {
        $branches = Branch::all();
        $devices = AttendanceDevice::all();
        return view('attendance::device.index', compact('branches', 'devices'));
    }

    /**
     * Get attendance device data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->attendanceDeviceService->getAttendanceDeviceDataTable($request);
    }

    /**
     * Store new attendance device
     */
    public function store(StoreAttendanceDeviceRequest $request)
    {
        $result = $this->attendanceDeviceService->saveAttendanceDevice($request->validated());
        return response()->json($result);
    }

    /**
     * Get single attendance device by ID
     */
    public function show($id)
    {
        $result = $this->attendanceDeviceService->getAttendanceDeviceById($id);
        return response()->json($result);
    }

    /**
     * Update existing attendance device
     */
    public function update(UpdateAttendanceDeviceRequest $request, $id)
    {
        $data = $request->validated();
        $data['attendance_device_id'] = $id;

        $result = $this->attendanceDeviceService->saveAttendanceDevice($data);
        return response()->json($result);
    }

    /**
     * Delete attendance device
     */
    public function destroy($id)
    {
        $result = $this->attendanceDeviceService->deleteAttendanceDevice($id);
        return response()->json($result);
    }
}