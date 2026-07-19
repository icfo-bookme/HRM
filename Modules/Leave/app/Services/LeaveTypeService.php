<?php

namespace Modules\Leave\Services;

use Modules\Leave\Models\LeaveType;
use Illuminate\Pagination\LengthAwarePaginator;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class LeaveTypeService
{
    /**
     * Paginated list of leave types.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return LeaveType::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['applicable_gender']) && $filters['applicable_gender'] !== 'All', function ($query) use ($filters) {
                $query->where('applicable_gender', $filters['applicable_gender']);
            })
            ->when($filters['sort_by'] ?? null, function ($query) use ($filters) {
                $direction = $filters['sort_direction'] ?? 'asc';
                $query->orderBy($filters['sort_by'], $direction);
            }, function ($query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Return DataTable JSON for server-side processing using Yajra DataTables.
     */
    public function getLeaveTypeDataTable(Request $request)
    {
        $query = LeaveType::select([
            'id',
            'name',
            'description',
            'days_per_year',
            'applicable_gender',
            'is_paid',
            'is_half_day_allowed',
            'carry_forward',
            'is_active',
            'color_code',
        ])->orderByDesc('id');

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        if ($request->applicable_gender && $request->applicable_gender !== '' && $request->applicable_gender !== 'All') {
            $query->where('applicable_gender', $request->applicable_gender);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('name', function ($type) {
                $color = $type->color_code ?? '#CBD5E1';
                $desc = e($type->description ?? '');
                $name = e($type->name);
                return '<div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full" style="background-color: ' . $color . '"></span>
                            <div>
                                <div class="text-sm font-medium text-slate-800">' . $name . '</div>
                                <div class="text-xs text-slate-500 line-clamp-1">' . $desc . '</div>
                            </div>
                        </div>';
            })
            ->editColumn('days_per_year', function ($type) {
                return '<span class="text-sm text-slate-600">' . $type->days_per_year . '</span>';
            })
            ->editColumn('applicable_gender', function ($type) {
                $gender = $type->applicable_gender;
                $colors = [
                    'All' => 'bg-blue-50 text-blue-600',
                    'Male' => 'bg-indigo-50 text-indigo-600',
                    'Female' => 'bg-pink-50 text-pink-600',
                ];
                $class = $colors[$gender] ?? 'bg-slate-50 text-slate-600';
                return '<span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded ' . $class . '">' . $gender . '</span>';
            })
            ->editColumn('is_paid', function ($type) {
                $html = '';
                if ($type->is_paid) {
                    $html .= '<span class="px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold uppercase rounded">Paid</span> ';
                }
                if ($type->is_half_day_allowed) {
                    $html .= '<span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase rounded">Half-Day</span> ';
                }
                if ($type->carry_forward) {
                    $html .= '<span class="px-2 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-bold uppercase rounded">Carry</span> ';
                }
                if (!$type->is_active) {
                    $html .= '<span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold uppercase rounded">Inactive</span>';
                }
                return $html ?: '<span class="text-xs text-slate-400">—</span>';
            })
            ->editColumn('is_active', function ($type) {
                return statusBadge($type->is_active);
            })
            ->addColumn('action', function ($type) {
                $editUrl = route('leave-types.edit', $type->id);
                $deleteUrl = route('leave-types.destroy', $type->id);

                return '<div class="flex justify-end gap-2">
                    <a href="' . $editUrl . '" class="p-1.5 text-slate-400 hover:text-indigo-600 transition" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure?\');" class="inline">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['name', 'days_per_year', 'applicable_gender', 'is_paid', 'is_active', 'action'])
            ->make(true);
    }

    /**
     * Find a leave type by ID, including soft-deleted.
     */
    public function find(int $id, bool $withTrashed = false): ?LeaveType
    {
        $query = LeaveType::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    /**
     * Create a new leave type.
     */
    public function create(array $data): LeaveType
    {
        return LeaveType::create($data);
    }

    /**
     * Update an existing leave type.
     */
    public function update(int $id, array $data): LeaveType
    {
        $leaveType = $this->find($id);
        $leaveType->update($data);

        return $leaveType->fresh();
    }

    /**
     * Soft delete a leave type.
     */
    public function delete(int $id): bool
    {
        $leaveType = $this->find($id);

        return $leaveType->delete();
    }

    /**
     * Restore a soft-deleted leave type.
     */
    public function restore(int $id): bool
    {
        $leaveType = LeaveType::onlyTrashed()->findOrFail($id);

        return $leaveType->restore();
    }

    /**
     * Force delete a leave type permanently.
     */
    public function forceDelete(int $id): bool
    {
        $leaveType = LeaveType::withTrashed()->findOrFail($id);

        return $leaveType->forceDelete();
    }

    /**
     * Get active leave types as key-value pairs for dropdowns.
     */
    public function getActiveList(): array
    {
        return LeaveType::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}