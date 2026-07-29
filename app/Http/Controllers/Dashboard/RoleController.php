<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreRoleRequest;
use App\Models\Permission;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display role listing page
     */
    public function index()
    {
        $permissions = Permission::all()->groupBy('group');

        return view('dashboard.roles.index', compact('permissions'));
    }

    /**
     * Get role data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->roleService->getRoleDataTable($request);
    }

    /**
     * Store new role
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        $result = $this->roleService->saveRole($validated);

        return response()->json($result);
    }

    /**
     * Get single role by ID
     */
    public function show($id)
    {
        $result = $this->roleService->getRoleById($id);

        return response()->json($result);
    }

    /**
     * Update existing role
     */
    public function update(StoreRoleRequest $request, $id)
    {
        $validated = $request->validated();
        $validated['role_id'] = $id;
        $result = $this->roleService->saveRole($validated);

        return response()->json($result);
    }

    /**
     * Delete role
     */
    public function destroy($id)
    {
        $result = $this->roleService->deleteRole($id);

        return response()->json($result);
    }
}
