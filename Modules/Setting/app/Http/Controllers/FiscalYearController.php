<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\StoreFiscalYearRequest;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;
use Modules\Setting\Services\FiscalYearService;

class FiscalYearController extends Controller
{
    protected FiscalYearService $fiscalYearService;

    public function __construct(FiscalYearService $fiscalYearService)
    {
        $this->fiscalYearService = $fiscalYearService;
    }

    public function index()
    {
        $companies = Company::all();

        return view('setting::fiscal-years.index', compact('companies'));
    }

    public function dataTable(Request $request)
    {
        return $this->fiscalYearService->getFiscalYearDataTable($request);
    }

    public function store(StoreFiscalYearRequest $request)
    {
        $result = $this->fiscalYearService->saveFiscalYear($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->fiscalYearService->getFiscalYearById($id);

        return response()->json($result);
    }

    public function update(StoreFiscalYearRequest $request, $id)
    {
        $validated = $request->validated();
        $validated['fy_id'] = $id;

        $result = $this->fiscalYearService->saveFiscalYear($validated);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->fiscalYearService->deleteFiscalYear($id);

        return response()->json($result);
    }
}
