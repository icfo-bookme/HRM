<?php

namespace Modules\Employee\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\SkillCategory;
use Yajra\DataTables\DataTables;

class SkillCategoryService
{
    public function getSkillCategoryDataTable(Request $request)
    {
        $query = SkillCategory::select(
            'skill_categories.id',
            'skill_categories.name',
            'skill_categories.description',
            'skill_categories.is_active',
        )->orderByDesc('skill_categories.id');

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('skill_categories.is_active', $request->is_active);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_active', function ($category) {
                return statusBadge($category->is_active);
            })
            ->addColumn('action', function ($category) {
                return view('components.action-buttons', [
                    'id'     => $category->id,
                    'edit'   => 'skillCategoryEdit',
                    'delete' => 'skillCategoryDelete',
                ])->render();
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function saveSkillCategory(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $categoryId = $data['category_id'] ?? null;

                if ($categoryId) {
                    $category = SkillCategory::findOrFail($categoryId);
                    $category->update($data);
                    $message = 'Skill category updated successfully.';
                    $status  = 'success';
                } else {
                    $category = SkillCategory::create($data);
                    $message = 'Skill category created successfully.';
                    $status  = 'success';
                }

                return [
                    'status'     => $status,
                    'message'    => $message,
                    'category'   => $category->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'   => 'error',
                'message'  => 'Error saving skill category: ' . $e->getMessage(),
                'category' => null,
            ];
        }
    }

    public function getSkillCategoryById(int $id): array
    {
        try {
            $category = SkillCategory::findOrFail($id);
            return [
                'status'   => 'success',
                'category' => $category,
            ];
        } catch (\Exception $e) {
            return [
                'status'   => 'error',
                'message'  => 'Skill category not found.',
                'category' => null,
            ];
        }
    }

    public function deleteSkillCategory(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                // Check if any skills reference this category
                $skillsCount = \Modules\Employee\Models\EmployeeSkill::where('category_id', $id)->count();
                if ($skillsCount > 0) {
                    return [
                        'status'  => 'error',
                        'message' => 'Cannot delete: ' . $skillsCount . ' skill(s) are using this category.',
                    ];
                }

                SkillCategory::findOrFail($id)->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Skill category deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting skill category: ' . $e->getMessage(),
            ];
        }
    }

    public function getActiveSkillCategories()
    {
        return SkillCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}