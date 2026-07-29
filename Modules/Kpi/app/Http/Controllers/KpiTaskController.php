<?php

namespace Modules\Kpi\Http\Controllers;

use App\Http\Requests\Kpi\CompleteTaskRequest;
use App\Http\Requests\Kpi\StoreTaskRequest;
use App\Http\Requests\Kpi\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\Models\Employee;
use Modules\Kpi\Models\KpiTask;
use Modules\Kpi\Services\KpiTaskService;

class KpiTaskController extends Controller
{
    protected KpiTaskService $taskService;

    public function __construct(KpiTaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employeeId = auth()->user()->employee?->id;

            return $this->taskService->getTaskDataTable($request, $employeeId);
        }

        $employees = Employee::with('personalInfo')->active()->get();

        return view('kpi::tasks.index', compact('employees'));
    }

    public function create()
    {
        $employees = Employee::with('personalInfo')->active()->get();

        return view('kpi::tasks.create', compact('employees'));
    }

    public function store(StoreTaskRequest $request)
    {
        $result = $this->taskService->createTask($request->validated());

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
    }

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

    public function edit(int $id)
    {
        $task = KpiTask::findOrFail($id);

        if (! in_array($task->status, ['Pending', 'In Progress'])) {
            return redirect()->route('kpi.tasks.index')
                ->with('error', 'Only pending or in-progress tasks can be edited.');
        }

        $employees = Employee::with('personalInfo')->active()->get();

        return view('kpi::tasks.edit', compact('task', 'employees'));
    }

    public function update(UpdateTaskRequest $request, int $id)
    {
        $result = $this->taskService->updateTask($id, $request->validated());

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.tasks.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function complete(CompleteTaskRequest $request, int $id)
    {
        $validated = $request->validated();

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
