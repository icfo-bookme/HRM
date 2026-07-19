<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Display permission listing page
     */
    public function index()
    {
        $groups = Permission::select('group')->distinct()->orderBy('group')->pluck('group');
        return view('dashboard.permissions.index', compact('groups'));
    }

    /**
     * Get permission data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->permissionService->getPermissionDataTable($request);
    }

    /**
     * Store new permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'group'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = $this->permissionService->savePermission($validated);
        return response()->json($result);
    }

    /**
     * Get single permission by ID
     */
    public function show($id)
    {
        $result = $this->permissionService->getPermissionById($id);
        return response()->json($result);
    }

    /**
     * Update existing permission
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'group'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['permission_id'] = $id;
        $result = $this->permissionService->savePermission($validated);
        return response()->json($result);
    }

    /**
     * Delete permission
     */
    public function destroy($id)
    {
        $result = $this->permissionService->deletePermission($id);
        return response()->json($result);
    }
}