<?php

namespace Modules\Designation\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Designation\Models\Designation;
use Yajra\DataTables\DataTables;

class DesignationService
{
    public function getDesignationDataTable(Request $request)
    {
        $query = Designation::with([ 'department', 'salaryGrade'])
            ->select(
                'designations.id',

                'designations.department_id',
                'designations.grade_id',
                'designations.title',
                'designations.level',
                'designations.is_active',
                'designations.created_at'
            )
            ->orderByDesc('designations.id');


        if ($request->department_id) {
            $query->where('designations.department_id', $request->department_id);
        }

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('designations.is_active', $request->is_active);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('grade_id', function (Designation $designation) {
                return $designation->salaryGrade?->name ?? '<span class="text-slate-400">N/A</span>';
            })
            ->editColumn('is_active', function (Designation $designation) {
                return statusBadge($designation->is_active);
            })
            ->editColumn('created_at', function (Designation $designation) {
                return $designation->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (Designation $designation) {
                return view('components.action-buttons', [
                    'id'     => $designation->id,
                    'edit'   => 'designationEdit',
                    'delete' => 'designationDelete',
                ])->render();
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function saveDesignation(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $designationId = $data['designation_id'] ?? null;

                if ($designationId) {
                    $designation = Designation::findOrFail($designationId);
                    $designation->update($data);
                    $message = 'Designation updated successfully.';
                } else {
                    $designation = Designation::create($data);
                    $message = 'Designation created successfully.';
                }

                return [
                    'status'      => 'success',
                    'message'     => $message,
                    'designation' => $designation->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving designation: ' . $e->getMessage(),
            ];
        }
    }

    public function getDesignationById(int $id): array
    {
        try {
            $designation = Designation::findOrFail($id);
            return [
                'status'      => 'success',
                'designation'=> $designation,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Designation not found.',
            ];
        }
    }

    public function deleteDesignation(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $designation = Designation::findOrFail($id);
                $designation->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Designation deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting designation: ' . $e->getMessage(),
            ];
        }
    }

    public function getActiveDesignations(): Collection
    {
        return Designation::where('is_active', true)
            ->orderBy('title')
            ->get();
    }
}
