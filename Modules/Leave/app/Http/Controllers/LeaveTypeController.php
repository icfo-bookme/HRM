<?php

namespace Modules\Leave\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Leave\Http\Requests\StoreLeaveTypeRequest;
use Modules\Leave\Http\Requests\UpdateLeaveTypeRequest;
use Modules\Leave\Services\LeaveTypeService;

class LeaveTypeController extends Controller
{
    public function __construct(protected LeaveTypeService $leaveTypeService) {}

    public function dataTable(Request $request)
    {
        return $this->leaveTypeService->getLeaveTypeDataTable($request);
    }

    public function index()
    {
        $filters = request()->only(['search', 'is_active', 'applicable_gender', 'sort_by', 'sort_direction']);
        $perPage = request()->input('per_page', 15);

        $leaveTypes = $this->leaveTypeService->paginate($filters, $perPage);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $leaveTypes->items(),
                'meta' => [
                    'current_page' => $leaveTypes->currentPage(),
                    'last_page' => $leaveTypes->lastPage(),
                    'per_page' => $leaveTypes->perPage(),
                    'total' => $leaveTypes->total(),
                ],
            ]);
        }

        return view('leave::leave-types.index', compact('leaveTypes'));
    }

    public function create(): View
    {
        return view('leave::leave-types.create');
    }

    public function store(StoreLeaveTypeRequest $request)
    {
        $leaveType = $this->leaveTypeService->create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Leave type created successfully.',
                'data' => $leaveType,
            ], 201);
        }

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    public function show(int $id)
    {
        $withTrashed = request()->boolean('with_trashed', false);
        $leaveType = $this->leaveTypeService->find($id, $withTrashed);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $leaveType,
            ]);
        }

        return view('leave::leave-types.show', compact('leaveType'));
    }

    public function edit(int $id): View
    {
        $leaveType = $this->leaveTypeService->find($id);

        return view('leave::leave-types.edit', compact('leaveType'));
    }

    public function update(UpdateLeaveTypeRequest $request, int $id)
    {
        $leaveType = $this->leaveTypeService->update($id, $request->validated());

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Leave type updated successfully.',
                'data' => $leaveType,
            ]);
        }

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->leaveTypeService->delete($id);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Leave type deleted successfully.',
            ]);
        }

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }

    public function restore(int $id)
    {
        $this->leaveTypeService->restore($id);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Leave type restored successfully.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Leave type restored successfully.');
    }

    public function forceDelete(int $id)
    {
        $this->leaveTypeService->forceDelete($id);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Leave type permanently deleted.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Leave type permanently deleted.');
    }

    public function activeList(): JsonResponse
    {
        return response()->json([
            'data' => $this->leaveTypeService->getActiveList(),
        ]);
    }
}
