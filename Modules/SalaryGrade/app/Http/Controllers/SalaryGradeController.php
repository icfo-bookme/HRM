<?php

namespace Modules\SalaryGrade\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SalaryGrade\Services\SalaryGradeService;
use Modules\SalaryGrade\Http\Requests\StoreSalaryGradeRequest;
use Modules\SalaryGrade\Http\Requests\UpdateSalaryGradeRequest;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;

class SalaryGradeController extends Controller
{
    protected $salaryGradeService;

    public function __construct(SalaryGradeService $salaryGradeService)
    {
        $this->salaryGradeService = $salaryGradeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::all();
        return view('salarygrade::index', compact('companies'));
    }

    /**
     * Get salary grade data for DataTable.
     */
    public function dataTable(Request $request)
    {
        return $this->salaryGradeService->getSalaryGradeDataTable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('salarygrade.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalaryGradeRequest $request)
    {
        $result = $this->salaryGradeService->saveSalaryGrade($request->validated());
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $result = $this->salaryGradeService->getSalaryGradeById($id);
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return redirect()->route('salarygrade.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalaryGradeRequest $request, $id)
    {
        $data = $request->validated();
        $data['salary_grade_id'] = $id;

        $result = $this->salaryGradeService->saveSalaryGrade($data);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->salaryGradeService->deleteSalaryGrade($id);
        return response()->json($result);
    }
}
