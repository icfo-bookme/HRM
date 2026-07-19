<?php

namespace Modules\Setting\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Setting\Models\FiscalYear;
use Modules\Company\Models\Company;
use Yajra\DataTables\DataTables;

class FiscalYearService
{
    public function getFiscalYearDataTable(Request $request)
    {
        $query = FiscalYear::with('company')
            ->select(
                'fiscal_years.id',
                'fiscal_years.company_id',
                'fiscal_years.label',
                'fiscal_years.start_date',
                'fiscal_years.end_date',
                'fiscal_years.is_current',
                'fiscal_years.locked',
                'fiscal_years.created_at',
            )
            ->orderByDesc('fiscal_years.id');

        if ($request->company_id !== null && $request->company_id !== '') {
            $query->where('fiscal_years.company_id', $request->company_id);
        }

        if ($request->is_current !== null && $request->is_current !== '') {
            $query->where('fiscal_years.is_current', $request->is_current);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('company', function ($fy) {
                return $fy->company?->name ?? 'N/A';
            })
            ->editColumn('start_date', function ($fy) {
                return $fy->start_date?->format('d M Y') ?? '—';
            })
            ->editColumn('end_date', function ($fy) {
                return $fy->end_date?->format('d M Y') ?? '—';
            })
            ->editColumn('is_current', function ($fy) {
                return statusBadge($fy->is_current);
            })
            ->editColumn('locked', function ($fy) {
                return $fy->locked
                    ? '<span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-red-50 text-red-600">Locked</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-green-50 text-green-600">Open</span>';
            })
            ->addColumn('action', function ($fy) {
                return view('components.action-buttons', [
                    'id'     => $fy->id,
                    'edit'   => 'fiscalYearEdit',
                    'delete' => 'fiscalYearDelete',
                ])->render();
            })
            ->rawColumns(['is_current', 'locked', 'action'])
            ->make(true);
    }

    public function saveFiscalYear(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $fyId = $data['fy_id'] ?? null;

                // If setting as current, unset other current records for same company
                if (!empty($data['is_current'])) {
                    FiscalYear::where('company_id', $data['company_id'])
                        ->where('is_current', true)
                        ->update(['is_current' => false]);
                }

                if ($fyId) {
                    $fy = FiscalYear::findOrFail($fyId);
                    $fy->update($data);
                    $message = 'Fiscal year updated successfully.';
                } else {
                    $fy = FiscalYear::create($data);
                    $message = 'Fiscal year created successfully.';
                }

                return [
                    'status'  => 'success',
                    'message' => $message,
                    'data'    => $fy->fresh()->load('company'),
                ];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMsg = $e->getMessage();
            if (str_contains($errorMsg, 'uk_fy_company_label') || str_contains($errorMsg, 'Duplicate entry')) {
                return [
                    'status'  => 'error',
                    'message' => 'A fiscal year with this label already exists for this company.',
                    'data'    => null,
                ];
            }
            return [
                'status'  => 'error',
                'message' => 'Error saving fiscal year: ' . $e->getMessage(),
                'data'    => null,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving fiscal year: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    public function getFiscalYearById(int $id): array
    {
        try {
            $fy = FiscalYear::with('company')->findOrFail($id);
            return [
                'status' => 'success',
                'data'   => $fy,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Fiscal year not found.',
                'data'    => null,
            ];
        }
    }

    public function deleteFiscalYear(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                FiscalYear::findOrFail($id)->delete();
                return [
                    'status'  => 'success',
                    'message' => 'Fiscal year deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting fiscal year: ' . $e->getMessage(),
            ];
        }
    }
}