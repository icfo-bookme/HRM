<?php

namespace Modules\SalaryGrade\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\SalaryGrade\Models\SalaryGrade;
use Yajra\DataTables\DataTables;

class SalaryGradeService
{
    public function getSalaryGradeDataTable(Request $request)
    {
        $query = SalaryGrade::select(
                'salary_grades.id',
                'salary_grades.name',
                'salary_grades.min_salary',
                'salary_grades.max_salary',
                'salary_grades.currency',
                'salary_grades.is_active',
                'salary_grades.created_at'
            )
            ->orderByDesc('salary_grades.id');


        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('salary_grades.is_active', $request->is_active);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function (SalaryGrade $salaryGrade) {
                return statusBadge($salaryGrade->is_active);
            })
            ->editColumn('min_salary', function (SalaryGrade $salaryGrade) {
                return number_format($salaryGrade->min_salary, 2) . ' ' . strtoupper($salaryGrade->currency);
            })
            ->editColumn('max_salary', function (SalaryGrade $salaryGrade) {
                return number_format($salaryGrade->max_salary, 2) . ' ' . strtoupper($salaryGrade->currency);
            })
            ->editColumn('created_at', function (SalaryGrade $salaryGrade) {
                return $salaryGrade->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (SalaryGrade $salaryGrade) {
                return view('components.action-buttons', [
                    'id'     => $salaryGrade->id,
                    'edit'   => 'salaryGradeEdit',
                    'delete' => 'salaryGradeDelete',
                ])->render();
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function saveSalaryGrade(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $salaryGradeId = $data['salary_grade_id'] ?? null;

                if ($salaryGradeId) {
                    $salaryGrade = SalaryGrade::findOrFail($salaryGradeId);
                    $salaryGrade->update($data);
                    $message = 'Salary grade updated successfully.';
                } else {
                    $salaryGrade = SalaryGrade::create($data);
                    $message = 'Salary grade created successfully.';
                }

                return [
                    'status'       => 'success',
                    'message'      => $message,
                    'salary_grade' => $salaryGrade->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving salary grade: ' . $e->getMessage(),
            ];
        }
    }

    public function getSalaryGradeById(int $id): array
    {
        try {
            $salaryGrade = SalaryGrade::findOrFail($id);
            return [
                'status'       => 'success',
                'salary_grade' => $salaryGrade,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Salary grade not found.',
            ];
        }
    }

    public function deleteSalaryGrade(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $salaryGrade = SalaryGrade::findOrFail($id);
                $salaryGrade->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Salary grade deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting salary grade: ' . $e->getMessage(),
            ];
        }
    }

    public function getActiveSalaryGrades(): Collection
    {
        return SalaryGrade::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
