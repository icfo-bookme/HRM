<?php

namespace Modules\Notice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notice\AcknowledgeNoticeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Branch\Models\Branch;
use Modules\Notice\Http\Requests\StoreNoticeRequest;
use Modules\Notice\Http\Requests\UpdateNoticeRequest;
use Modules\Notice\Models\Notice;
use Modules\Notice\Models\NoticeAcknowledgement;
use Modules\Notice\Models\NoticeView;
use Modules\Notice\Services\NoticeService;

class NoticeController extends Controller
{
    protected NoticeService $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

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

        $noticeIds = $notices->pluck('id')->toArray();
        $existingViewNoticeIds = NoticeView::whereIn('notice_id', $noticeIds)
            ->where('employee_id', $employeeId)
            ->pluck('notice_id')
            ->toArray();

        $newViewNoticeIds = array_diff($noticeIds, $existingViewNoticeIds);

        if (! empty($newViewNoticeIds)) {
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

        $branches = Branch::where('is_active', true)->pluck('name', 'id');

        return view('notice::create', compact('noticeTypes', 'priorities', 'branches'));
    }

    public function storeFromPage(StoreNoticeRequest $request)
    {
        $notice = $this->noticeService->saveNoticeFromPage($request->validated(), $request);

        if ($notice) {
            return redirect()->route('notice.manage')->with('success', 'Notice created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create notice.')->withInput();
    }

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

        $branches = Branch::where('is_active', true)->pluck('name', 'id');

        return view('notice::edit', compact('notice', 'noticeTypes', 'priorities', 'branches'));
    }

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

    public function detail($id)
    {
        $notice = Notice::with('acknowledgements.employee.personalInfo')
            ->withCount('views')
            ->findOrFail($id);

        $user = Auth::user();
        $employeeId = $user->employee_id;

        NoticeView::firstOrCreate([
            'notice_id' => $id,
            'employee_id' => $employeeId,
        ], [
            'created_at' => now(),
        ]);

        $myAcknowledgement = NoticeAcknowledgement::where('notice_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        $viewers = NoticeView::with('employee.personalInfo')
            ->where('notice_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notice::detail', compact('notice', 'myAcknowledgement', 'viewers'));
    }

    public function acknowledge(AcknowledgeNoticeRequest $request, $id)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;

        $existing = NoticeAcknowledgement::where('notice_id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        if ($existing) {
            $existing->update([
                'comment' => $request->comment,
            ]);

            return redirect()->route('notice.detail', $id)
                ->with('success', 'Your acknowledgment has been updated.');
        }

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
