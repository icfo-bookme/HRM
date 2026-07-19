<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class RoleService
{
    public function getRoleDataTable(Request $request)
    {
        $query = Role::query()
            ->withCount('permissions', 'users');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_system', function ($role) {
                return $role->is_system
                    ? '<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">System</span>'
                    : '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Custom</span>';
            })
            ->editColumn('created_at', function ($role) {
                return $role->created_at->format('d M Y');
            })
            ->addColumn('action', function ($role) {
                $html = view('components.action-buttons', [
                    'id'     => $role->id,
                    'edit'   => 'roleEdit',
                    'delete' => $role->is_system ? null : 'roleDelete',
                ])->render();
                // If delete is null, only show edit button
                if ($role->is_system) {
                    $html = '<div class="flex space-x-2 justify-center">' .
                        '<button onclick="roleEdit(' . $role->id . ')" class="bg-blue-900 text-white px-2 py-1 rounded text-sm hover:bg-blue-600 mr-2"><i class="fa fa-pencil"></i></button>' .
                        '<span class="text-gray-400 text-xs italic">System</span>' .
                        '</div>';
                }
                return $html;
            })
            ->rawColumns(['is_system', 'action'])
            ->make(true);
    }

    public function saveRole(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $roleId = $data['role_id'] ?? null;
                $permissions = $data['permissions'] ?? [];

                if ($roleId) {
                    $role = Role::findOrFail($roleId);
                    $role->update([
                        'name'        => $data['name'],
                        'description' => $data['description'] ?? null,
                    ]);
                    $message = 'Role updated successfully.';
                } else {
                    $role = Role::create([
                        'name'        => $data['name'],
                        'slug'        => Str::slug($data['name']),
                        'description' => $data['description'] ?? null,
                        'is_system'   => false,
                    ]);
                    $message = 'Role created successfully.';
                }

                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }

                return [
                    'status'  => 'success',
                    'message' => $message,
                    'role'    => $role->fresh()->loadCount('permissions', 'users'),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving role: ' . $e->getMessage(),
                'role'    => null,
            ];
        }
    }

    public function getRoleById(int $id): array
    {
        try {
            $role = Role::with('permissions')->withCount('permissions', 'users')->findOrFail($id);
            return [
                'status' => 'success',
                'role'   => $role,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Role not found.',
                'role'    => null,
            ];
        }
    }

    public function deleteRole(int $id): array
    {
        try {
            $role = Role::findOrFail($id);
            if ($role->is_system) {
                return [
                    'status'  => 'error',
                    'message' => 'System roles cannot be deleted.',
                ];
            }
            if ($role->users()->count() > 0) {
                return [
                    'status'  => 'error',
                    'message' => 'Cannot delete role with assigned users.',
                ];
            }
            $role->permissions()->detach();
            $role->delete();
            return [
                'status'  => 'success',
                'message' => 'Role deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting role: ' . $e->getMessage(),
            ];
        }
    }
}