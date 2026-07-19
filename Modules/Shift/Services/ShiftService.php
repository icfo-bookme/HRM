<?php

namespace Modules\Shift\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Shift\Models\Shift;
use Yajra\DataTables\DataTables;

class ShiftService
{
    public function getShiftDataTable(Request $request)
    {
        $query = Shift::select(
            'shifts.id',

            'shifts.name',

            'shifts.start_time',
            'shifts.end_time',
            'shifts.break_minutes',
            'shifts.grace_in_min',
            'shifts.grace_out_min',
            'shifts.work_hours',
            'shifts.is_night_shift',
            'shifts.is_flexible',
            'shifts.is_active',
            'shifts.created_at'
        )
            ->orderByDesc('shifts.id');


        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('shifts.is_active', $request->is_active);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_night_shift', function (Shift $shift) {
                return statusBadge($shift->is_night_shift);
            })
            ->editColumn('is_flexible', function (Shift $shift) {
                return statusBadge($shift->is_flexible);
            })
            ->editColumn('is_active', function (Shift $shift) {
                return statusBadge($shift->is_active);
            })
            ->editColumn('created_at', function (Shift $shift) {
                return $shift->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function (Shift $shift) {
                return view('components.action-buttons', [
                    'id'     => $shift->id,
                    'edit'   => 'shiftEdit',
                    'delete' => 'shiftDelete',
                ])->render();
            })
            ->rawColumns(['is_night_shift', 'is_flexible', 'is_active', 'action'])
            ->make(true);
    }

    public function saveShift(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $shiftId = $data['shift_id'] ?? null;

                if ($shiftId) {
                    $shift = Shift::findOrFail($shiftId);
                    $shift->update($data);
                    $message = 'Shift updated successfully.';
                } else {
                    $shift = Shift::create($data);
                    $message = 'Shift created successfully.';
                }

                return [
                    'status'  => 'success',
                    'message' => $message,
                    'shift'   => $shift->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving shift: ' . $e->getMessage(),
                'shift'   => null,
            ];
        }
    }

    public function getShiftById(int $id): array
    {
        try {
            $shift = Shift::findOrFail($id);

            return [
                'status' => 'success',
                'shift'  => $shift,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Shift not found.',
                'shift'   => null,
            ];
        }
    }

    public function deleteShift(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $shift = Shift::findOrFail($id);
                $shift->update(['deleted_at' => now()]);

                return [
                    'status'  => 'success',
                    'message' => 'Shift deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting shift: ' . $e->getMessage(),
            ];
        }
    }

    public function getActiveShifts(): Collection
    {
        return Shift::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getShiftsByCompany(int $companyId): Collection
    {
        return Shift::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }
}
