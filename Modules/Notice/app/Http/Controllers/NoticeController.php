<?php

namespace Modules\Notice\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Notice\Services\NoticeService;
use Modules\Notice\Http\Requests\StoreNoticeRequest;
use Modules\Notice\Http\Requests\UpdateNoticeRequest;
use Modules\Notice\Models\Notice;
use Modules\Notice\Models\NoticeAcknowledgement;
use Modules\Notice\Models\NoticeView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    protected $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    /**
     * Public-facing list view - all notices in detailed card format
     * Auto-tracks views: when employee visits, mark all active notices as "seen"
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;

        $notices = Notice::withCount(['views', 'acknowledgements'])
            ->with(['acknowledgements' => function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            }])
            ->where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->paginate(10);

        // Auto-track views: mark all fetched notices as "seen" by this employee
        $noticeIds = $notices->pluck('id')->toArray();
        $existingViewNoticeIds = NoticeView::whereIn('notice_id', $noticeIds)
            ->where('employee_id', $employeeId)
            ->pluck('notice_id')
            ->toArray();

        $newViewNoticeIds = array_diff($noticeIds, $existingViewNoticeIds);

        if (!empty($newViewNoticeIds)) {
            $now = now();
            $views = [];
            foreach ($newViewNoticeIds as $nid) {
                $views[] = [
                    'notice_id' => $nid,
                    'employee_id' => $employeeId,
                    'created_at' => $now,
                ];
            }
            NoticeView::insert($views);
        }

        return view('notice::list', compact('notices'));
    }

    /**
     * Data table management view (Manage Notice)
     */
    public function manage()
    {
        $noticeTypes = [
            'General' => 'General',
            'HR' => 'HR',
            'Holiday' => 'Holiday',
            'Attendance' => 'Attendance',
            'Payroll' => 'Payroll',
            'Policy' => 'Policy',
            'Training' => 'Training',
            'Event' => 'Event',
            'Emergency' => 'Emergency',
        ];

        $priorities = [
            'Low' => 'Low',
            'Medium' => 'Medium',
            'High' => 'High',
            'Urgent' => 'Urgent',
        ];

        $pinnedStatus = [
            '1' => 'Pinned',
            '0' => 'Not Pinned',
        ];

        $statuses = [
            '1' => 'Active',
            '0' => 'Inactive',
        ];

        return view('notice::index', compact('noticeTypes', 'priorities', 'pinnedStatus', 'statuses'));
    }

    /**
     * Show the create notice page (separate page, not drawer)
     */
    public function create()
    {
        $noticeTypes = [
            'General' => 'General',
            'HR' => 'HR',
            'Holiday' => 'Holiday',
            'Attendance' => 'Attendance',
            'Payroll' => 'Payroll',
            'Policy' => 'Policy',
            'Training' => 'Training',
            'Event' => 'Event',
            'Emergency' => 'Emergency',
        ];

        $priorities = [
            'Low' => 'Low',
            'Medium' => 'Medium',
            'High' => 'High',
            'Urgent' => 'Urgent',
        ];

        $branches = \Modules\Branch\Models\Branch::where('is_active', true)->pluck('name', 'id');

        return view('notice::create', compact('noticeTypes', 'priorities', 'branches'));
    }

    /**
     * Store notice from the dedicated create page (not AJAX)
     */
    public function storeFromPage(StoreNoticeRequest $request)
    {
        $notice = $this->noticeService->saveNoticeFromPage($request->validated(), $request);

        if ($notice) {
            return redirect()->route('notice.manage')->with('success', 'Notice created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create notice.')->withInput();
    }

    /**
     * Show the edit notice page
     */
    public function edit($id)
    {
        $notice = Notice::findOrFail($id);

        $noticeTypes = [
            'General' => 'General',
            'HR' => 'HR',
            'Holiday' => 'Holiday',
            'Attendance' => 'Attendance',
            'Payroll' => 'Payroll',
            'Policy' => 'Policy',
            'Training' => 'Training',
            'Event' => 'Event',
            'Emergency' => 'Emergency',
        ];

        $priorities = [
            'Low' => 'Low',
            'Medium' => 'Medium',
            'High' => 'High',
            'Urgent' => 'Urgent',
        ];

        $branches = \Modules\Branch\Models\Branch::where('is_active', true)->pluck('name', 'id');

        return view('notice::edit', compact('notice', 'noticeTypes', 'priorities', 'branches'));
    }

    /**
     * Update notice from the dedicated edit page (not AJAX)
     */
    public function updateFromPage(UpdateNoticeRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        $notice = $this->noticeService->saveNoticeFromPage($data, $request);

        if ($notice) {
            return redirect()->route('notice.manage')->with('success', 'Notice updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update notice.')->withInput();
    }

    public function dataTable(Request $request)
    {
        return $this->noticeService->getNoticeDataTable($request);
    }

    public function store(StoreNoticeRequest $request)
    {
        $result = $this->noticeService->saveNotice($request->validated(), $request);
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->noticeService->getNoticeById($id);
        return response()->json($result);
    }

    /**
     * Public single notice detail view
     * Auto-tracks view when employee opens the detail page
     */
    public function detail($id)
    {
        $notice = Notice::with('acknowledgements.employee.personalInfo')
            ->withCount('views')
            ->findOrFail($id);

        $user = Auth::user();
        $employeeId = $user->employee_id;

        // Auto-track view on detail page
        NoticeView::firstOrCreate([
            'notice_id' => $id,
            'employee_id' => $employeeId,
        ], [
            'created_at' => now(),
        ]);

        $myAcknowledgement = NoticeAcknowledgement::where('notice_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        // Get all viewers with employee info for "Seen by" section
        $viewers = NoticeView::with('employee.personalInfo')
            ->where('notice_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notice::detail', compact('notice', 'myAcknowledgement', 'viewers'));
    }

    /**
     * Acknowledge a notice (with optional comment)
     */
    public function acknowledge(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $employeeId = $user->employee_id;

        // Check if already acknowledged
        $existing = NoticeAcknowledgement::where('notice_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        if ($existing) {
            // Update the comment if already acknowledged
            $existing->update([
                'comment' => $request->comment,
            ]);

            return redirect()->route('notice.detail', $id)
                ->with('success', 'Your acknowledgment has been updated.');
        }

        // Create new acknowledgment
        NoticeAcknowledgement::create([
            'notice_id' => $id,
            'employee_id' => $employeeId,
            'comment' => $request->comment,
            'created_at' => now(),
        ]);

        return redirect()->route('notice.detail', $id)
            ->with('success', 'Notice acknowledged successfully.');
    }

    public function update(UpdateNoticeRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        $result = $this->noticeService->saveNotice($data, $request);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->noticeService->deleteNotice($id);
        return response()->json($result);
    }
}
