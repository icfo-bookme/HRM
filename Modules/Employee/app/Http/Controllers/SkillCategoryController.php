<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Employee\Services\SkillCategoryService;
use Modules\Employee\Http\Requests\StoreSkillCategoryRequest;
use Modules\Employee\Http\Requests\UpdateSkillCategoryRequest;
use Illuminate\Http\Request;

class SkillCategoryController extends Controller
{
    protected SkillCategoryService $skillCategoryService;

    public function __construct(SkillCategoryService $skillCategoryService)
    {
        $this->skillCategoryService = $skillCategoryService;
    }

    public function index()
    {
        return view('employee::skill-categories.index');
    }

    public function dataTable(Request $request)
    {
        return $this->skillCategoryService->getSkillCategoryDataTable($request);
    }

    public function store(StoreSkillCategoryRequest $request)
    {
        $result = $this->skillCategoryService->saveSkillCategory($request->validated());
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->skillCategoryService->getSkillCategoryById($id);
        return response()->json($result);
    }

    public function update(UpdateSkillCategoryRequest $request, $id)
    {
        $data = $request->validated();
        $data['category_id'] = $id;

        $result = $this->skillCategoryService->saveSkillCategory($data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->skillCategoryService->deleteSkillCategory($id);
        return response()->json($result);
    }
}