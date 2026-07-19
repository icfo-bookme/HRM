<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

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
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

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