<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Company\Services\CompanyService;
use Modules\Company\Http\Requests\StoreCompanyRequest;
use Modules\Company\Http\Requests\UpdateCompanyRequest;
use Modules\Company\Models\Company;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('company::index');
    }

    public function dataTable(Request $request)
    {
        return $this->companyService->getCompanyDataTable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('company.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $result = $this->companyService->saveCompany($request->validated());
        return response()->json($result);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $result = $this->companyService->getCompanyById($id);
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return redirect()->route('company.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, $id)
    {
        $data = $request->validated();
        $data['company_id'] = $id;
        $result = $this->companyService->saveCompany($data);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->companyService->deleteCompany($id);
        return response()->json($result);
    }
}
