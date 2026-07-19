<?php

namespace Modules\Salary\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Salary\Models\EmployeeSalaryStructure;
use Yajra\DataTables\DataTables;

class EmployeeSalaryStructureService
{
    /**
     * Get employee salary structure data for DataTable
     */
    public function getEmployeeSalaryStructureDataTable(Request $request)
    {
        $query = EmployeeSalaryStructure::with(['employee', 'component'])
            ->select(
                'employee_salary_structure.id',
                'employee_salary_structure.employee_id',
                'employee_salary_structure.component_id',
                'employee_salary_structure.amount',
                'employee_salary_structure.effective_from',
                'employee_salary_structure.effective_to',
                'employee_salary_structure.is_percentage',
                'employee_salary_structure.created_at'
            );

        if ($request->filled('employee_id')) {
            $query->where('employee_salary_structure.employee_id', $request->employee_id);
        }

        if ($request->filled('component_id')) {
            $query->where('employee_salary_structure.component_id', $request->component_id);
        }

        return DataTables::of($query)
            ->editColumn('employee_id', function ($structure) {
                return $structure->employee?->employee_code . ' - ' . ($structure->employee?->personalInfo?->full_name ?? 'N/A');
            })
            ->editColumn('component_id', function ($structure) {
                return $structure->component?->name ?? 'N/A';
            })
            ->editColumn('amount', function ($structure) {
                $value = number_format($structure->amount, 2);
                if ($structure->is_percentage) {
                    return $value . ' %';
                }
                return $value;
            })
            ->editColumn('effective_from', function (EmployeeSalaryStructure $structure) {
                return $structure->effective_from->format('d M Y');
            })
            ->editColumn('effective_to', function (EmployeeSalaryStructure $structure) {
                return $structure->effective_to ? $structure->effective_to->format('d M Y') : 'Ongoing';
            })
            ->editColumn('created_at', function (EmployeeSalaryStructure $structure) {
                return $structure->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (EmployeeSalaryStructure $structure) {
                return view('components.action-buttons', [
                    'id'     => $structure->id,
                    'edit'   => 'employeeSalaryStructureEdit',
                    'delete' => 'employeeSalaryStructureDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

   
    public function saveEmployeeSalaryStructure(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $structureId = $data['structure_id'] ?? null;

                if ($structureId) {
                    $structure = EmployeeSalaryStructure::findOrFail($structureId);
                    $structure->update($data);
                    $message = 'Employee salary structure updated successfully.';
                    $status  = 'success';
                } else {
                    $structure = EmployeeSalaryStructure::create($data);
                    $message  = 'Employee salary structure created successfully.';
                    $status   = 'success';
                }

                return [
                    'status'    => $status,
                    'message'   => $message,
                    'structure' => $structure->fresh()->load(['employee', 'component']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'    => 'error',
                'message'   => 'Error saving employee salary structure: ' . $e->getMessage(),
                'structure' => null,
            ];
        }
    }

    /**
     * Get employee salary structure by ID
     */
    public function getEmployeeSalaryStructureById(int $id): array
    {
        try {
            $structure = EmployeeSalaryStructure::with(['employee', 'component'])->findOrFail($id);
            return [
                'status'    => 'success',
                'structure' => $structure,
            ];
        } catch (\Exception $e) {
            return [
                'status'    => 'error',
                'message'   => 'Employee salary structure not found.',
                'structure' => null,
            ];
        }
    }

    /**
     * Delete employee salary structure
     */
    public function deleteEmployeeSalaryStructure(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $structure = EmployeeSalaryStructure::findOrFail($id);
                $structure->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Employee salary structure deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting employee salary structure: ' . $e->getMessage(),
            ];
        }
    }

  
    public function getStructureByEmployee(int $employeeId)
    {
        return EmployeeSalaryStructure::with('component')
            ->where('employee_id', $employeeId)
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->get();
    }
}