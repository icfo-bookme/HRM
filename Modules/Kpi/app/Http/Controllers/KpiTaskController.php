<?php

namespace Modules\Kpi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Kpi\Services\KpiTaskService;
use Modules\Kpi\Models\KpiTask;
use Modules\Employee\Models\Employee;

class KpiTaskController extends Controller
{
    protected KpiTaskService $taskService;

    public function __construct(KpiTaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    { 
        if ($request->ajax()) {
            $employeeId = auth()->user()->employee?->id;
            return $this->taskService->getTaskDataTable($request, $employeeId);
        }

        $employees = Employee::with('personalInfo')->active()->get();
        return view('kpi::tasks.index', compact('employees'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $employees = Employee::with('personalInfo')->active()->get();
        return view('kpi::tasks.create', compact('employees'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_score' => 'required|numeric|min:0.1|max:999999.99',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'deadline' => 'nullable|date|after_or_equal:today',
        ]);

        $result = $this->taskService->createTask($validated);

       if ($result['status'] === 'success') {
        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'task' => $result['task'],
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => $result['message'],
    ], 422);
     

        return back()->with('error', $result['message'])->withInput();
    }

    /**
     * Display the specified task.
     */
    public function show(int $id)
    {
        $task = KpiTask::with(['employee.personalInfo', 'employee.department', 'assignedBy.personalInfo'])
            ->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => true,
                'task' => $task->toArray(),
            ]);
        }

        return view('kpi::tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(int $id)
    {
        $task = KpiTask::findOrFail($id);

        if (!in_array($task->status, ['Pending', 'In Progress'])) {
            return redirect()->route('kpi.tasks.index')
                ->with('error', 'Only pending or in-progress tasks can be edited.');
        }

        $employees = Employee::with('personalInfo')->active()->get();
        return view('kpi::tasks.edit', compact('task', 'employees'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_score' => 'required|numeric|min:0.1|max:999999.99',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:Pending,In Progress',
        ]);

        $result = $this->taskService->updateTask($id, $validated);

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.tasks.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    /**
     * Mark task as completed.
     */
    public function complete(Request $request, int $id)
    {
        $validated = $request->validate([
            'obtained_score' => 'required|numeric|min:0|max:999999.99',
            'completion_note' => 'nullable|string|max:1000',
        ]);

        $result = $this->taskService->completeTask(
            $id,
            $validated['obtained_score'],
            $validated['completion_note'] ?? null
        );

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.tasks.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(int $id)
    {
        $result = $this->taskService->deleteTask($id);

        if (request()->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.tasks.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
