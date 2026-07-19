<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Setting\Services\FiscalYearService;
use Modules\Company\Models\Company;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'  => 'required|integer|exists:companies,id',
            'label'       => 'required|string|max:20',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'is_current'  => 'nullable|boolean',
            'locked'      => 'nullable|boolean',
        ]);

        $result = $this->fiscalYearService->saveFiscalYear($validated);
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->fiscalYearService->getFiscalYearById($id);
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'company_id'  => 'required|integer|exists:companies,id',
            'label'       => 'required|string|max:20',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'is_current'  => 'nullable|boolean',
            'locked'      => 'nullable|boolean',
        ]);

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