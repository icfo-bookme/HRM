<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PermissionService
{
    public function getPermissionDataTable(Request $request)
    {
        $query = Permission::query()
            ->select('id', 'name', 'slug', 'group', 'description', 'created_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($perm) {
                return $perm->created_at->format('d M Y');
            })
            ->addColumn('action', function ($perm) {
                return view('components.action-buttons', [
                    'id'     => $perm->id,
                    'edit'   => 'permissionEdit',
                    'delete' => 'permissionDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function savePermission(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $permId = $data['permission_id'] ?? null;

                if ($permId) {
                    $permission = Permission::findOrFail($permId);
                    $permission->update([
                        'name'        => $data['name'],
                        'description' => $data['description'] ?? null,
                    ]);
                    $message = 'Permission updated successfully.';
                } else {
                    $slug = Str::slug($data['name']);
                    // Check if slug already exists
                    if (Permission::where('slug', $slug)->exists()) {
                        return [
                            'status'     => 'error',
                            'message'    => 'A permission with this name already exists (slug: ' . $slug . ').',
                            'permission' => null,
                        ];
                    }
                    $permission = Permission::create([
                        'name'        => $data['name'],
                        'slug'        => $slug,
                        'group'       => $data['group'] ?? 'General',
                        'description' => $data['description'] ?? null,
                    ]);
                    $message = 'Permission created successfully.';
                }

                return [
                    'status'     => 'success',
                    'message'    => $message,
                    'permission' => $permission->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'     => 'error',
                'message'    => 'Error saving permission: ' . $e->getMessage(),
                'permission' => null,
            ];
        }
    }

    public function getPermissionById(int $id): array
    {
        try {
            $permission = Permission::findOrFail($id);
            return [
                'status'     => 'success',
                'permission' => $permission,
            ];
        } catch (\Exception $e) {
            return [
                'status'     => 'error',
                'message'    => 'Permission not found.',
                'permission' => null,
            ];
        }
    }

    public function deletePermission(int $id): array
    {
        try {
            $permission = Permission::findOrFail($id);
            // Detach from all roles first
            $permission->roles()->detach();
            $permission->delete();
            return [
                'status'  => 'success',
                'message' => 'Permission deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting permission: ' . $e->getMessage(),
            ];
        }
    }
}