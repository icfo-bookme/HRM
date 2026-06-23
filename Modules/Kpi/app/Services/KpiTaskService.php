<?php

namespace Modules\Kpi\Services;

use Illuminate\Support\Facades\DB;
use Modules\Kpi\Models\KpiTask;
use Modules\Employee\Models\Employee;
use Yajra\DataTables\DataTables;

class KpiTaskService
{
    /**
     * Get tasks data for DataTable
     */
    public function getTaskDataTable($request, ?int $employeeId = null)
    {
        $query = KpiTask::with(['employee.personalInfo', 'assignedBy.personalInfo']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('employee_id', function ($task) {
                $emp = $task->employee;
                return $emp ? ($emp->employee_code . ' - ' . ($emp->personalInfo?->full_name ?? 'N/A')) : 'N/A';
            })
            ->editColumn('assigned_by', function ($task) {
                return $task->assignedBy?->personalInfo?->full_name ?? 'N/A';
            })
            ->editColumn('target_score', fn($task) => number_format($task->target_score, 1))
            ->editColumn('obtained_score', fn($task) => $task->obtained_score ? number_format($task->obtained_score, 1) : '-')
            ->editColumn('assigned_date', fn($task) => $task->assigned_date->format('d M Y'))
            ->editColumn('deadline', fn($task) => $task->deadline?->format('d M Y') ?? '-')
            ->editColumn('priority', function ($task) {
                $colors = [
                    'Low' => 'bg-gray-100 text-gray-600',
                    'Medium' => 'bg-blue-100 text-blue-700',
                    'High' => 'bg-orange-100 text-orange-700',
                    'Critical' => 'bg-red-100 text-red-700',
                ];
                $color = $colors[$task->priority] ?? 'bg-gray-100 text-gray-600';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $task->priority . '</span>';
            })
            ->editColumn('status', function ($task) {
                $colors = [
                    'Pending' => 'bg-yellow-100 text-yellow-700',
                    'In Progress' => 'bg-blue-100 text-blue-700',
                    'Completed' => 'bg-green-100 text-green-700',
                    'Cancelled' => 'bg-gray-100 text-gray-500',
                    'Overdue' => 'bg-red-100 text-red-700',
                ];
                $color = $colors[$task->status] ?? 'bg-gray-100 text-gray-600';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $task->status . '</span>';
            })
            ->addColumn('action', function ($task) {
                $btn = '<a href="' . route('kpi.tasks.show', $task->id) . '" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 mr-1" title="View"><i class="fas fa-eye"></i></a>';

                if (in_array($task->status, ['Pending', 'In Progress'])) {
                    $btn .= '<a href="' . route('kpi.tasks.edit', $task->id) . '" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 mr-1" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button onclick="completeTask(' . $task->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 mr-1" title="Complete"><i class="fas fa-check"></i></button>';
                    $btn .= '<button onclick="deleteTask(' . $task->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200" title="Delete"><i class="fas fa-trash"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['priority', 'status', 'action'])
            ->make(true);
    }

    /**
     * Create a new task
     */
    public function createTask(array $data): array
    {
        try {
            $task = KpiTask::create([
                'employee_id' => $data['employee_id'],
                'assigned_by' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'target_score' => $data['target_score'],
                'priority' => $data['priority'] ?? 'Medium',
                'assigned_date' => $data['assigned_date'] ?? now(),
                'deadline' => $data['deadline'] ?? null,
                'status' => 'Pending',
            ]);

            return [
                'status' => 'success',
                'message' => 'Task assigned successfully.',
                'task' => $task->load(['employee.personalInfo', 'assignedBy.personalInfo']),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to create task: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update a task
     */
    public function updateTask(int $id, array $data): array
    {
        try {
            $task = KpiTask::findOrFail($id);

            if (!in_array($task->status, ['Pending', 'In Progress'])) {
                return ['status' => 'error', 'message' => 'Only pending or in-progress tasks can be edited.'];
            }

            $task->update($data);

            return [
                'status' => 'success',
                'message' => 'Task updated successfully.',
                'task' => $task->fresh()->load(['employee.personalInfo', 'assignedBy.personalInfo']),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to update task: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mark task as completed
     */
    public function completeTask(int $id, float $obtainedScore, ?string $note = null): array
    {
        try {
            $task = KpiTask::findOrFail($id);

            if ($task->status === 'Completed') {
                return ['status' => 'error', 'message' => 'Task is already completed.'];
            }

            $task->update([
                'status' => 'Completed',
                'obtained_score' => $obtainedScore,
                'completed_at' => now(),
                'completion_note' => $note,
            ]);

            return [
                'status' => 'success',
                'message' => 'Task marked as completed.',
                'task' => $task->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to complete task: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a task
     */
    public function deleteTask(int $id): array
    {
        try {
            $task = KpiTask::findOrFail($id);

            if (!in_array($task->status, ['Pending', 'In Progress'])) {
                return ['status' => 'error', 'message' => 'Only pending or in-progress tasks can be deleted.'];
            }

            $task->delete();

            return ['status' => 'success', 'message' => 'Task deleted successfully.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to delete task: ' . $e->getMessage()];
        }
    }

    /**
     * Get task statistics
     */
    public function getTaskStatistics(?int $employeeId = null): array
    {
        $query = KpiTask::query();

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $total = $query->count();
        $pending = (clone $query)->whereIn('status', ['Pending', 'In Progress'])->count();
        $completed = (clone $query)->where('status', 'Completed')->count();
        $overdue = (clone $query)->where('status', 'Overdue')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'completed' => $completed,
            'overdue' => $overdue,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }
}
