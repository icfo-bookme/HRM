<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\UserService;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display user listing page
     */
    public function index()
    {
        $roles = Role::all();
        $employees = Employee::all();
        return view('dashboard.users.index', compact('roles','employees'));
    }

    /**
     * Get user data for DataTable AJAX
     */
    public function dataTable(Request $request)
    {
        return $this->userService->getUserDataTable($request);
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:8'],
            'role_id'     => ['nullable', 'exists:roles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $result = $this->userService->saveUser($validated);
        return response()->json($result);
    }

    /**
     * Get single user by ID
     */
    public function show($id)
    {
        $result = $this->userService->getUserById($id);
        return response()->json($result);
    }

    /**
     * Update existing user
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password'    => ['nullable', 'string', 'min:8'],
            'role_id'     => ['nullable', 'exists:roles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $validated['user_id'] = $id;
        $result = $this->userService->saveUser($validated);
        return response()->json($result);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);
        return response()->json($result);
    }
}