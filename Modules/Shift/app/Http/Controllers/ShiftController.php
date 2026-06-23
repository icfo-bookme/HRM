<?php

namespace Modules\Shift\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Company\Models\Company;
use Modules\Shift\Http\Requests\StoreShiftRequest;
use Modules\Shift\Http\Requests\UpdateShiftRequest;
use Modules\Shift\Services\ShiftService;

class ShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::all();

        return view('shift::index', compact('companies'));
    }

    /**
     * Get shift data for DataTable AJAX.
     */
    public function dataTable(Request $request)
    {
        return $this->shiftService->getShiftDataTable($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        $result = $this->shiftService->saveShift($request->validated());
        return response()->json($result);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $result = $this->shiftService->getShiftById($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftRequest $request, $id)
    {
        $data = $request->validated();
        $data['shift_id'] = $id;

        $result = $this->shiftService->saveShift($data);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->shiftService->deleteShift($id);
        return response()->json($result);
    }
}
