<?php

namespace Modules\SalaryGrade\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;
use Modules\SalaryGrade\Http\Requests\StoreSalaryGradeRequest;
use Modules\SalaryGrade\Http\Requests\UpdateSalaryGradeRequest;
use Modules\SalaryGrade\Services\SalaryGradeService;

class SalaryGradeController extends Controller
{
    protected SalaryGradeService $salaryGradeService;

    public function __construct(SalaryGradeService $salaryGradeService)
    {
        $this->salaryGradeService = $salaryGradeService;
    }

    public function index(Request $request)
    {
        $companies = Company::all();

        return view('salarygrade::index', compact('companies'));
    }

    public function dataTable(Request $request)
    {
        return $this->salaryGradeService->getSalaryGradeDataTable($request);
    }

    public function create()
    {
        return redirect()->route('salarygrade.index');
    }

    public function store(StoreSalaryGradeRequest $request)
    {
        $result = $this->salaryGradeService->saveSalaryGrade($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->salaryGradeService->getSalaryGradeById($id);

        return response()->json($result);
    }

    public function edit($id)
    {
        return redirect()->route('salarygrade.index');
    }

    public function update(UpdateSalaryGradeRequest $request, $id)
    {
        $data = $request->validated();
        $data['salary_grade_id'] = $id;

        $result = $this->salaryGradeService->saveSalaryGrade($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->salaryGradeService->deleteSalaryGrade($id);

        return response()->json($result);
    }
}
