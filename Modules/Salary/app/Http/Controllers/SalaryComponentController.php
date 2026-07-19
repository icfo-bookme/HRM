<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Salary\Services\SalaryComponentService;
use Modules\Salary\Http\Requests\StoreSalaryComponentRequest;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    protected $salaryComponentService;

    public function __construct(SalaryComponentService $salaryComponentService)
    {
        $this->salaryComponentService = $salaryComponentService;
    }

    /**
     * Display salary component listing page
     */
    public function index(Request $request)
    {
        return view('salary::salary-components.index');
    }

    /**
     * Get salary component data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->salaryComponentService->getSalaryComponentDataTable($request);
    }

    /**
     * Store new salary component
     */
    public function store(StoreSalaryComponentRequest $request)
    {
        $result = $this->salaryComponentService->saveSalaryComponent($request->validated());
        return response()->json($result);
    }

    /**
     * Get single salary component by ID
     */
    public function show($id)
    {
        $result = $this->salaryComponentService->getSalaryComponentById($id);
        return response()->json($result);
    }

    /**
     * Update existing salary component
     */
    public function update(StoreSalaryComponentRequest $request, $id)
    {
        $data = $request->validated();
        $data['component_id'] = $id;

        $result = $this->salaryComponentService->saveSalaryComponent($data);
        return response()->json($result);
    }

    /**
     * Delete salary component
     */
    public function destroy($id)
    {
        $result = $this->salaryComponentService->deleteSalaryComponent($id);
        return response()->json($result);
    }
}