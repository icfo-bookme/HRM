<?php

namespace Modules\Notice\Services;

use Modules\Notice\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class NoticeService
{
    public function getNoticeDataTable(Request $request)
    {
        $query = Notice::select('notices.*');

        // Apply filters
        if ($request->filled('notice_type')) {
            $query->where('notice_type', $request->notice_type);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('is_pinned')) {
            $query->where('is_pinned', $request->is_pinned);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return view('components.action-buttons', [
                    'id' => $row->id,
                    'edit' => 'noticeEdit',
                    'delete' => 'noticeDelete'
                ])->render();
            })
            ->addColumn('attachment', function ($row) {
                if ($row->attachment_path) {
                    $fileName = basename($row->attachment_path);
                    return '<a href="' . Storage::url($row->attachment_path) . '" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline text-xs">' . $fileName . '</a>';
                }
                return '-';
            })
            ->editColumn('notice_type', function ($row) {
                $colors = [
                    'General'    => 'bg-slate-100 text-slate-800',
                    'HR'         => 'bg-blue-100 text-blue-800',
                    'Holiday'    => 'bg-green-100 text-green-800',
                    'Attendance' => 'bg-yellow-100 text-yellow-800',
                    'Payroll'    => 'bg-purple-100 text-purple-800',
                    'Policy'     => 'bg-indigo-100 text-indigo-800',
                    'Training'   => 'bg-pink-100 text-pink-800',
                    'Event'      => 'bg-orange-100 text-orange-800',
                    'Emergency'  => 'bg-red-100 text-red-800',
                ];
                $color = $colors[$row->notice_type] ?? 'bg-slate-100 text-slate-700';
                return '<span class="' . $color . ' text-xs font-medium px-2.5 py-0.5 rounded-full">' . $row->notice_type . '</span>';
            })
            ->editColumn('priority', function ($row) {
                $colors = [
                    'Low'    => 'bg-green-100 text-green-800',
                    'Medium' => 'bg-yellow-100 text-yellow-800',
                    'High'   => 'bg-orange-100 text-orange-800',
                    'Urgent' => 'bg-red-100 text-red-800',
                ];
                $color = $colors[$row->priority] ?? 'bg-slate-100 text-slate-700';
                return '<span class="' . $color . ' text-xs font-medium px-2.5 py-0.5 rounded-full">' . $row->priority . '</span>';
            })
            ->editColumn('is_pinned', function ($row) {
                return $row->is_pinned
                    ? '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pinned</span>'
                    : '-';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>'
                    : '<span class="bg-slate-100 text-slate-700 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>';
            })
            ->editColumn('publish_date', function ($row) {
                return $row->publish_date ? $row->publish_date->format('d M Y H:i') : '';
            })
            ->editColumn('expiry_date', function ($row) {
                return $row->expiry_date ? $row->expiry_date->format('d M Y H:i') : '-';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->rawColumns(['action', 'notice_type', 'priority', 'is_pinned', 'is_active', 'attachment'])
            ->make(true);
    }

    public function saveNotice(array $data, $request = null)
    {
        return DB::transaction(function () use ($data, $request) {
            $data['is_popup'] = isset($data['is_popup']) ? (bool)$data['is_popup'] : false;
            $data['is_pinned'] = isset($data['is_pinned']) ? (bool)$data['is_pinned'] : false;
            $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;

            // Handle file upload
            if ($request && $request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('notices', 'public');
                $data['attachment_path'] = $path;
            }

            if (!empty($data['id'])) {
                $notice = Notice::findOrFail($data['id']);

                // Handle removal of existing attachment
                if (isset($data['remove_attachment']) && $data['remove_attachment'] == '1') {
                    if ($notice->attachment_path) {
                        Storage::disk('public')->delete($notice->attachment_path);
                    }
                    $data['attachment_path'] = null;
                }

                $notice->update($data);

                return [
                    'status' => true,
                    'message' => 'Notice updated successfully.',
                    'data' => $notice
                ];
            } else {
                $notice = Notice::create($data);
                return [
                    'status' => true,
                    'message' => 'Notice created successfully.',
                    'data' => $notice
                ];
            }
        });
    }

    public function getNoticeById($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return [
                'status' => false,
                'message' => 'Notice not found!'
            ];
        }

        return [
            'status' => true,
            'data' => $notice
        ];
    }

    public function deleteNotice($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {
            return [
                'status' => false,
                'message' => 'Notice not found or already deleted.'
            ];
        }

        // Delete associated attachment
        if ($notice->attachment_path) {
            Storage::disk('public')->delete($notice->attachment_path);
        }

        $notice->update([
            'deleted_at' => now()
        ]);

        return [
            'status' => true,
            'message' => 'Notice deleted successfully.'
        ];
    }
}