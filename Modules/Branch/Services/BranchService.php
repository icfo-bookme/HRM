<?php

namespace Modules\Branch\Services;

use Modules\Branch\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BranchService
{

    public function getBranchDataTable(Request $request)
    {
        $query = Branch::with(['company'])->select('branches.*');

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return view('components.action-buttons', [
                    'id' => $row->id,
                    'edit' => 'branchEdit',
                    'delete' => 'branchDelete'
                ])->render();
            })

            ->editColumn('is_head_office', function ($row) {
                return $row->is_head_office
                    ? '<span class="bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Head Office</span>'
                    : '<span class="bg-slate-100 text-slate-700 text-xs font-medium px-2.5 py-0.5 rounded-full">Branch</span>';
            })
            ->editColumn('is_active', function ($department) {
                return statusBadge($department->is_active);
            })
            ->editColumn('created_at', function ($row) {
                 return $row->created_at->format('d M Y H:i');
            })
            ->rawColumns(['action', 'is_head_office', 'is_active'])
            ->make(true);
    }

    public function saveBranch(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['is_head_office'] = isset($data['is_head_office']) ? (bool)$data['is_head_office'] : false;
            $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : true;


            if ($data['is_head_office'] === true) {
                Branch::where('is_head_office', true)
                    ->when(!empty($data['id']), function ($query) use ($data) {
                        return $query->where('id', '!=', $data['id']);
                    })
                    ->update(['is_head_office' => false]);
            }


            if (isset($data['metadata'])) {
                $data['metadata'] = is_array($data['metadata']) ? $data['metadata'] : json_decode($data['metadata'], true);
            }

            if (!empty($data['id'])) {

                $branch = Branch::findOrFail($data['id']);
                $branch->update($data);

                return [
                    'status' => true,
                    'message' => 'Branch data updated successfully.',
                    'data' => $branch
                ];
            } else {
                $branch = Branch::create($data);
                return [
                    'status' => true,
                    'message' => 'Branch data inserted successfully.',
                    'data' => $branch
                ];
            }
        });
    }


    public function getBranchById($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return [
                'status' => false,
                'message' => 'Branch data not found!'
            ];
        }

        return [
            'status' => true,
            'data' => $branch
        ];
    }


    public function deleteBranch($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return [
                'status' => false,
                'message' => 'Branch not found or already deleted.'
            ];
        }


        if ($branch->is_head_office) {
            return [
                'status' => false,
                'message' => 'You cannot delete the Head Office. Assign another branch as Head Office first.'
            ];
        }

        $branch->update([
            'deleted_at' => now()
        ]);

        return [
            'status' => true,
            'message' => 'Branch data deleted successfully.'
        ];
    }
}
