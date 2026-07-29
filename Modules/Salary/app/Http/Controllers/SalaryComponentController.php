<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Salary\Http\Requests\StoreSalaryComponentRequest;
use Modules\Salary\Services\SalaryComponentService;

class SalaryComponentController extends Controller
{
    protected SalaryComponentService $salaryComponentService;

    public function __construct(SalaryComponentService $salaryComponentService)
    {
        $this->salaryComponentService = $salaryComponentService;
    }

    public function index(Request $request)
    {
        return view('salary::salary-components.index');
    }

    public function dataTable(Request $request)
    {
        return $this->salaryComponentService->getSalaryComponentDataTable($request);
    }

    public function store(StoreSalaryComponentRequest $request)
    {
        $result = $this->salaryComponentService->saveSalaryComponent($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->salaryComponentService->getSalaryComponentById($id);

        return response()->json($result);
    }

    public function update(StoreSalaryComponentRequest $request, $id)
    {
        $data = $request->validated();
        $data['component_id'] = $id;

        $result = $this->salaryComponentService->saveSalaryComponent($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->salaryComponentService->deleteSalaryComponent($id);

        return response()->json($result);
    }
}
