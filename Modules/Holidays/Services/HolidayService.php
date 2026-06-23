<?php

namespace Modules\Holidays\Services;

use Modules\Holidays\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class HolidayService
{
    public function getHolidayDataTable(Request $request)
    {
        $query = Holiday::select('holidays.*');

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return view('components.action-buttons', [
                    'id' => $row->id,
                    'edit' => 'holidayEdit',
                    'delete' => 'holidayDelete'
                ])->render();
            })
            ->editColumn('holiday_date', function ($row) {
                return $row->holiday_date ? $row->holiday_date->format('d M Y') : '';
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date ? $row->end_date->format('d M Y') : '';
            })
            ->editColumn('holiday_type', function ($row) {
                $colors = [
                    'Public' => 'bg-blue-100 text-blue-800',
                    'Government' => 'bg-red-100 text-red-800',
                    'Company' => 'bg-green-100 text-green-800',
                    'Optional' => 'bg-yellow-100 text-yellow-800',
                    'Religious' => 'bg-purple-100 text-purple-800',
                    'Festival' => 'bg-pink-100 text-pink-800',
                ];
                $color = $colors[$row->holiday_type] ?? 'bg-slate-100 text-slate-700';
                return '<span class="' . $color . ' text-xs font-medium px-2.5 py-0.5 rounded-full">' . $row->holiday_type . '</span>';
            })
            ->editColumn('is_recurring', function ($row) {
                return $row->is_recurring
                    ? '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Yes</span>'
                    : '<span class="bg-slate-100 text-slate-700 text-xs font-medium px-2.5 py-0.5 rounded-full">No</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->rawColumns(['action', 'holiday_type', 'is_recurring'])
            ->make(true);
    }

    public function saveHoliday(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['is_recurring'] = isset($data['is_recurring']) ? (bool)$data['is_recurring'] : false;
            $data['yearly_recurring'] = isset($data['yearly_recurring']) ? (bool)$data['yearly_recurring'] : false;

            if (!empty($data['id'])) {
                $holiday = Holiday::findOrFail($data['id']);
                $holiday->update($data);

                return [
                    'status' => true,
                    'message' => 'Holiday updated successfully.',
                    'data' => $holiday
                ];
            } else {
                $holiday = Holiday::create($data);
                return [
                    'status' => true,
                    'message' => 'Holiday created successfully.',
                    'data' => $holiday
                ];
            }
        });
    }

    public function getHolidayById($id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {
            return [
                'status' => false,
                'message' => 'Holiday not found!'
            ];
        }

        return [
            'status' => true,
            'data' => $holiday
        ];
    }

    public function saveHolidaysBatch(array $data, array $dates)
    {
        return DB::transaction(function () use ($data, $dates) {
            $data['is_recurring'] = isset($data['is_recurring']) ? (bool)$data['is_recurring'] : false;
            $data['yearly_recurring'] = isset($data['yearly_recurring']) ? (bool)$data['yearly_recurring'] : false;

            $created = 0;
            foreach ($dates as $date) {
                $holidayData = array_merge($data, [
                    'holiday_date' => $date,
                    'end_date' => null,
                ]);

                // Check if a holiday with same name already exists on this date
                $existing = Holiday::whereNull('deleted_at')
                    ->where('name', $data['name'])
                    ->where('holiday_date', $date)
                    ->first();

                if (!$existing) {
                    Holiday::create($holidayData);
                    $created++;
                }
            }

            return [
                'status' => true,
                'message' => $created > 0
                    ? "{$created} holiday(s) created successfully."
                    : 'Holidays already exist for the selected dates.',
            ];
        });
    }

    public function deleteHoliday($id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {
            return [
                'status' => false,
                'message' => 'Holiday not found or already deleted.'
            ];
        }

        $holiday->update([
            'deleted_at' => now()
        ]);

        return [
            'status' => true,
            'message' => 'Holiday deleted successfully.'
        ];
    }
}
