<?php

namespace Modules\Branch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Branch\Http\Requests\StoreBranchRequest;
use Modules\Branch\Http\Requests\UpdateBranchRequest;
use Modules\Branch\Services\BranchService;

class BranchController extends Controller
{
    protected BranchService $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    public function index(Request $request)
    {
        return view('branch::index');
    }

    public function dataTable(Request $request)
    {
        return $this->branchService->getBranchDataTable($request);
    }

    public function store(StoreBranchRequest $request)
    {
        $result = $this->branchService->saveBranch($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->branchService->getBranchById($id);

        return response()->json($result);
    }

    public function update(UpdateBranchRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        $result = $this->branchService->saveBranch($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->branchService->deleteBranch($id);

        return response()->json($result);
    }
}
