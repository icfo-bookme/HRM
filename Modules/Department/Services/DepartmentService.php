<?php

namespace Modules\Department\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Department\Models\Department;
use Yajra\DataTables\DataTables;

class DepartmentService
{

    public function getDepartmentDataTable(Request $request)
    {
        $query = Department::with('branch')
            ->select(
                'departments.id',
                'departments.branch_id',
                'departments.code',
                'departments.name',
                'departments.email',
                'departments.phone',
                'departments.is_active',
                'departments.created_at',
                'departments.updated_at'
            )
            ->orderByDesc('departments.sort_order');

       

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('departments.is_active', $request->is_active);
        }


        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function ($department) {
                return statusBadge($department->is_active);
            })
            ->editColumn('created_at', function (Department $department) {
                return $department->created_at->format('d M Y H:i');
            })

            ->addColumn('action', function (Department $department) {
                return view('components.action-buttons', [
                    'id'     => $department->id,
                    'edit'   => 'departmentEdit',
                    'delete' => 'departmentDelete',
                ])->render();
            })
            ->rawColumns(['is_active', 'email', 'action'])
            ->make(true);
    }


    public function saveDepartment(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $departmentId = $data['department_id'] ?? null;

                if ($departmentId) {
                    // Update existing department
                    $department = Department::findOrFail($departmentId);
                    $department->update($data);
                    $message = 'Department updated successfully.';
                    $status  = 'success';
                } else {
                    // Create new department
                    $department = Department::create($data);
                    $message    = 'Department created successfully.';
                    $status     = 'success';
                }

                return [
                    'status'     => $status,
                    'message'    => $message,
                    'department' => $department->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'     => 'error',
                'message'    => 'Error saving department: ' . $e->getMessage(),
                'department' => null,
            ];
        }
    }

    public function getDepartmentById(int $id): array
    {
        try {
            $department = Department::findOrFail($id);
            return [
                'status'     => 'success',
                'department' => $department,
            ];
        } catch (\Exception $e) {
            return [
                'status'     => 'error',
                'message'    => 'Department not found.',
                'department' => null,
            ];
        }
    }

    public function deleteDepartment(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $department = Department::findOrFail($id);

                

                $department->update(['deleted_at' => now()]);

                return [
                    'status'  => 'success',
                    'message' => 'Department deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting department: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all active departments
     *
     * @return Collection
     */
    public function getActiveDepartments(): Collection
    {
        return Department::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get departments by company
     *
     * @param int $companyId
     * @return Collection
     */
    public function getDepartmentsByCompany(int $companyId): Collection
    {
        return Department::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get parent departments (departments without parent_id or root level)
     *
     * @param int|null $companyId
     * @return Collection
     */
    public function getParentDepartments(?int $companyId = null): Collection
    {
        $query = Department::whereNull('parent_id')->orderBy('name');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    /**
     * Get child departments of a parent
     *
     * @param int $parentId
     * @return Collection
     */
    public function getChildDepartments(int $parentId): Collection
    {
        return Department::where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
    }
}
