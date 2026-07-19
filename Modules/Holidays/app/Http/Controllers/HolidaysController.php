<?php

namespace Modules\Holidays\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Holidays\Services\HolidayService;
use Modules\Holidays\Services\HolidayAssignmentService;
use Modules\Holidays\Http\Requests\StoreHolidayRequest;
use Modules\Holidays\Http\Requests\UpdateHolidayRequest;
use Modules\Holidays\Http\Requests\StoreHolidayAssignmentRequest;
use Modules\Holidays\Http\Requests\UpdateHolidayAssignmentRequest;
use Illuminate\Http\Request;

class HolidaysController extends Controller
{
    protected $holidayService;
    protected $assignmentService;

    public function __construct(HolidayService $holidayService, HolidayAssignmentService $assignmentService)
    {
        $this->holidayService = $holidayService;
        $this->assignmentService = $assignmentService;
    }

    // ---- Holidays CRUD ----

    public function index(Request $request)
    {
        return view('holidays::index');
    }

    public function dataTable(Request $request)
    {
        return $this->holidayService->getHolidayDataTable($request);
    }

    public function store(StoreHolidayRequest $request)
    {
        $result = $this->holidayService->saveHoliday($request->validated());
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->holidayService->getHolidayById($id);
        return response()->json($result);
    }

    public function update(UpdateHolidayRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        $result = $this->holidayService->saveHoliday($data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->holidayService->deleteHoliday($id);
        return response()->json($result);
    }

    // ---- Holiday Calendar ----

    public function calendar(Request $request)
    {
        return view('holidays::calendar');
    }

    public function calendarData(Request $request)
    {
        $holidays = \Modules\Holidays\Models\Holiday::whereNull('deleted_at')->get();

        $events = [];
        foreach ($holidays as $h) {
            $colorMap = [
                'Public' => '#3b82f6',
                'Government' => '#ef4444',
                'Company' => '#22c55e',
                'Optional' => '#eab308',
                'Religious' => '#a855f7',
                'Festival' => '#ec4899',
            ];
            $color = $colorMap[$h->holiday_type] ?? '#6b7280';

            $events[] = [
                'id' => 'holiday-' . $h->id,
                'title' => $h->name,
                'start' => $h->holiday_date->format('Y-m-d'),
                'end' => $h->end_date ? $h->end_date->copy()->addDay()->format('Y-m-d') : null,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'holiday_id' => $h->id,
                    'holiday_type' => $h->holiday_type,
                    'applicable_to' => $h->applicable_to,
                    'description' => $h->description,
                    'is_recurring' => $h->is_recurring,
                    'yearly_recurring' => $h->yearly_recurring,
                ]
            ];
        }

        return response()->json($events);
    }

    public function calendarStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:300',
            'holiday_type' => 'required|string',
            'applicable_to' => 'required|string',
            'selected_dates' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $selectedDates = json_decode($request->selected_dates, true);

        if (!$selectedDates || !is_array($selectedDates) || count($selectedDates) === 0) {
            return response()->json([
                'status' => false,
                'message' => 'No dates selected.'
            ]);
        }

        sort($selectedDates);
        $firstDate = $selectedDates[0];
        $lastDate = end($selectedDates);

        $data = [
            'name' => $request->name,
            'holiday_type' => $request->holiday_type,
            'applicable_to' => $request->applicable_to,
            'description' => $request->description,
            'is_recurring' => $request->has('is_recurring') ? (bool)$request->is_recurring : false,
            'yearly_recurring' => $request->has('yearly_recurring') ? (bool)$request->yearly_recurring : false,
        ];

        // Always save as a single range from first selected date to last selected date
        if (count($selectedDates) > 1) {
            $data['holiday_date'] = $firstDate;
            $data['end_date'] = $lastDate;
            $result = $this->holidayService->saveHoliday($data);
        } else {
            $data['holiday_date'] = $firstDate;
            $data['end_date'] = null;
            $result = $this->holidayService->saveHoliday($data);
        }

        return response()->json($result);
    }

    // ---- Holiday Assignments ----

    public function assignIndex(Request $request)
    {
        return view('holidays::assign');
    }

    public function assignDataTable(Request $request)
    {
        return $this->assignmentService->getAssignmentDataTable($request);
    }

    public function assignStore(StoreHolidayAssignmentRequest $request)
    {
        $result = $this->assignmentService->saveAssignment($request->validated());
        return response()->json($result);
    }

    public function assignShow($id)
    {
        $result = $this->assignmentService->getAssignmentById($id);
        return response()->json($result);
    }

    public function assignUpdate(UpdateHolidayAssignmentRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        $result = $this->assignmentService->saveAssignment($data);
        return response()->json($result);
    }

    public function assignDestroy($id)
    {
        $result = $this->assignmentService->deleteAssignment($id);
        return response()->json($result);
    }
}
