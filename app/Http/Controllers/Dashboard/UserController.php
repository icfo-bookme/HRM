<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreUserRequest;
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

        return view('dashboard.users.index', compact('roles', 'employees'));
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
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
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
    public function update(StoreUserRequest $request, $id)
    {
        $validated = $request->validated();
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
