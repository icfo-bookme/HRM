<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Company\Http\Requests\StoreCompanyRequest;
use Modules\Company\Http\Requests\UpdateCompanyRequest;
use Modules\Company\Services\CompanyService;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        return view('company::index');
    }

    public function dataTable(Request $request)
    {
        return $this->companyService->getCompanyDataTable($request);
    }

    public function create()
    {
        return redirect()->route('company.index');
    }

    public function store(StoreCompanyRequest $request)
    {
        $result = $this->companyService->saveCompany($request->validated());

        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->companyService->getCompanyById($id);

        return response()->json($result);
    }

    public function edit($id)
    {
        return redirect()->route('company.index');
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        $data = $request->validated();
        $data['company_id'] = $id;
        $result = $this->companyService->saveCompany($data);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->companyService->deleteCompany($id);

        return response()->json($result);
    }
}
