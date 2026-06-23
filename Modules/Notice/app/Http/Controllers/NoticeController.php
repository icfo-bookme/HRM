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