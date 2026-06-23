<?php

namespace Modules\Company\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Modules\Company\Models\Company;

class CompanyService
{
    public function getCompanyDataTable(Request $request)
    {
        $query = Company::select([
            'companies.id',
            'companies.name',
            'companies.legal_name',
            'companies.city',
            'companies.phone',
            'companies.email',
            'companies.is_active',
            'companies.created_at',
        ])->orderByDesc('companies.id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function (Company $company) {
                return $company->is_active ? statusBadge($company->is_active) : statusBadge($company->is_active);
            })
            ->editColumn('created_at', function (Company $company) {
                return $company->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (Company $company) {
                return view('components.action-buttons', [
                    'id' => $company->id,
                    'edit' => 'companyEdit',
                    'delete' => 'companyDelete',
                ])->render();
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function saveCompany(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $companyId = $data['company_id'] ?? null;

                $payload = $data;

                if ($companyId) {
                    $company = Company::findOrFail($companyId);
                    $company->update($payload);
                    $message = 'Company updated successfully.';
                } else {
                    $company = Company::create($payload);
                    $message = 'Company created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'company' => $company->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving company: ' . $e->getMessage(),
            ];
        }
    }

    public function getCompanyById(int $id): array
    {
        try {
            $company = Company::findOrFail($id);
            return [
                'status' => 'success',
                'company' => $company,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Company not found.',
            ];
        }
    }

    public function deleteCompany(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $company = Company::findOrFail($id);
                $company->delete();
                return [
                    'status' => 'success',
                    'message' => 'Company deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting company: ' . $e->getMessage(),
            ];
        }
    }
}
