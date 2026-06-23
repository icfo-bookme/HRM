<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserService
{
    public function getUserDataTable(Request $request)
    {
        $query = User::with('role')
            ->select('users.id', 'users.name', 'users.email', 'users.role_id', 'users.created_at');

        if ($search = $request->get('search')['value'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('role_id', function ($user) {
                if (!$user->role) {
                    return '<span class="text-gray-400 text-xs">No Role</span>';
                }
                $colors = [
                    'admin' => 'bg-red-100 text-red-700',
                    'manager' => 'bg-blue-100 text-blue-700',
                ];
                $class = $colors[$user->role->slug] ?? 'bg-green-100 text-green-700';
                return '<span class="px-2.5 py-1 rounded-full text-xs font-medium ' . $class . '">' . e($user->role->name) . '</span>';
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('d M Y');
            })
            ->addColumn('action', function ($user) {
                return view('components.action-buttons', [
                    'id'     => $user->id,
                    'edit'   => 'userEdit',
                    'delete' => 'userDelete',
                ])->render();
            })
            ->rawColumns(['role_id', 'action'])
            ->make(true);
    }

    public function saveUser(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $userId = $data['user_id'] ?? null;

                if ($userId) {
                    $user = User::findOrFail($userId);
                    if (isset($data['password']) && !empty($data['password'])) {
                        $data['password'] = Hash::make($data['password']);
                    } else {
                        unset($data['password']);
                    }
                    unset($data['user_id']);
                    $user->update($data);
                    $message = 'User updated successfully.';
                } else {
                    $data['password'] = Hash::make($data['password']);
                    $user = User::create($data);
                    $message = 'User created successfully.';
                }

                return [
                    'status'  => 'success',
                    'message' => $message,
                    'user'    => $user->fresh()->load('role'),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving user: ' . $e->getMessage(),
                'user'    => null,
            ];
        }
    }

    public function getUserById(int $id): array
    {
        try {
            $user = User::with('role')->findOrFail($id);
            return [
                'status' => 'success',
                'user'   => $user,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'User not found.',
                'user'    => null,
            ];
        }
    }

    public function deleteUser(int $id): array
    {
        try {
            $user = User::findOrFail($id);
            if ($user->isAdmin()) {
                return [
                    'status'  => 'error',
                    'message' => 'Cannot delete admin users.',
                ];
            }
            $user->delete();
            return [
                'status'  => 'success',
                'message' => 'User deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting user: ' . $e->getMessage(),
            ];
        }
    }
}