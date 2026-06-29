<?php

namespace Modules\Notice\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Notice\Services\NoticeService;
use Modules\Notice\Http\Requests\StoreNoticeRequest;
use Modules\Notice\Http\Requests\UpdateNoticeRequest;
use Modules\Notice\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    protected $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    /**
     * Public-facing list view - all notices in detailed card format
     */
    public function index(Request $request)
    {
        $notices = Notice::where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('publish_date', 'desc')
            ->paginate(10);

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
     */
    public function detail($id)
    {
        $notice = Notice::findOrFail($id);
        return view('notice::detail', compact('notice'));
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