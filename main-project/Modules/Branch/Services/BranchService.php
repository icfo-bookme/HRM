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
        
        $query = Branch::with(['company']); 

        return DataTables::of($query)
            ->addColumn('action', function ($row) {

                return '
                    <div class="flex space-x-2 justify-center">
                        <button onclick="branchEdit('.$row->id.')" class="bg-blue-500 text-white px-2 py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fa fa-pencil"></i> Edit
                        </button>
                        <button onclick="branchDelete('.$row->id.')" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>';
            })
            ->editColumn('is_head_office', function ($row) {
                return $row->is_head_office 
                    ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Head Office</span>' 
                    : '<span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">Branch</span>';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active 
                    ? '<span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Active</span>' 
                    : '<span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded">Inactive</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
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
                Branch::where('company_id', $data['company_id'])
                    ->where('is_head_office', true)
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
            } 
            
            else {
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

        $branch->delete();
        
        return [
            'status' => true, 
            'message' => 'Branch data deleted successfully.'
        ];
    }
}