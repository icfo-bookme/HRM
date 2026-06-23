<?php

namespace Modules\Salary\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Salary\Models\SalaryComponent;
use Yajra\DataTables\DataTables;

class SalaryComponentService
{
    /**
     * Get salary component data for DataTable
     */
    public function getSalaryComponentDataTable(Request $request)
    {
        $query = SalaryComponent::select(
            'salary_components.id',
            'salary_components.name',
            'salary_components.type',
            'salary_components.category',
            'salary_components.calculation_type',
            'salary_components.default_value',
            'salary_components.is_taxable',
            'salary_components.is_pf_basis',
            'salary_components.is_active',
            'salary_components.show_in_slip',
            'salary_components.display_order',
            'salary_components.created_at',
            'salary_components.updated_at'
        )->orderBy('salary_components.display_order');

        if ($request->filled('type')) {
            $query->where('salary_components.type', $request->type);
        }

        if ($request->filled('is_active') && $request->is_active !== '') {
            $query->where('salary_components.is_active', $request->is_active);
        }

        return DataTables::of($query)
            
            ->editColumn('default_value', function ($component) {
                return number_format($component->default_value, 2);
            })
            ->editColumn('is_taxable', function ($component) {
                return $component->is_taxable
                    ? '<span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Yes</span>'
                    : '<span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">No</span>';
            })
            ->editColumn('is_pf_basis', function ($component) {
                return $component->is_pf_basis
                    ? '<span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Yes</span>'
                    : '<span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">No</span>';
            })
            ->editColumn('is_active', function ($component) {
                return $component->is_active
                    ? '<span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Active</span>'
                    : '<span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Inactive</span>';
            })
            ->editColumn('show_in_slip', function ($component) {
                return $component->show_in_slip
                    ? '<span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Yes</span>'
                    : '<span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">No</span>';
            })
            ->editColumn('created_at', function (SalaryComponent $component) {
                return $component->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (SalaryComponent $component) {
                return view('components.action-buttons', [
                    'id'     => $component->id,
                    'edit'   => 'salaryComponentEdit',
                    'delete' => 'salaryComponentDelete',
                ])->render();
            })
            ->rawColumns(['is_taxable', 'is_pf_basis', 'is_active', 'show_in_slip', 'action'])
            ->make(true);
    }

    /**
     * Save salary component (create or update)
     */
    public function saveSalaryComponent(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $componentId = $data['component_id'] ?? null;

                if ($componentId) {
                    $component = SalaryComponent::findOrFail($componentId);
                    $component->update($data);
                    $message = 'Salary component updated successfully.';
                    $status  = 'success';
                } else {
                    $component = SalaryComponent::create($data);
                    $message  = 'Salary component created successfully.';
                    $status   = 'success';
                }

                return [
                    'status'           => $status,
                    'message'          => $message,
                    'salary_component' => $component->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'           => 'error',
                'message'          => 'Error saving salary component: ' . $e->getMessage(),
                'salary_component' => null,
            ];
        }
    }

    /**
     * Get salary component by ID
     */
    public function getSalaryComponentById(int $id): array
    {
        try {
            $component = SalaryComponent::findOrFail($id);
            return [
                'status'           => 'success',
                'salary_component' => $component,
            ];
        } catch (\Exception $e) {
            return [
                'status'           => 'error',
                'message'          => 'Salary component not found.',
                'salary_component' => null,
            ];
        }
    }

    /**
     * Delete salary component
     */
    public function deleteSalaryComponent(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $component = SalaryComponent::findOrFail($id);
                $component->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Salary component deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting salary component: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all active components ordered
     */
    public function getActiveComponents()
    {
        return SalaryComponent::active()->ordered()->get();
    }

    /**
     * Get components by type
     */
    public function getComponentsByType(string $type)
    {
        return SalaryComponent::active()->ofType($type)->ordered()->get();
    }
}