<?php

namespace Modules\Kpi\Http\Controllers;

use App\Http\Requests\Kpi\StoreReviewRequest;
use Illuminate\Routing\Controller;
use Modules\Employee\Models\Employee;
use Modules\Kpi\Models\KpiMonthlyReview;
use Modules\Kpi\Models\KpiMonthlyScore;
use Modules\Kpi\Services\KpiReviewService;

class KpiReviewController extends Controller
{
    protected KpiReviewService $reviewService;

    public function __construct(KpiReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $reviews = KpiMonthlyReview::with([
            'employee.personalInfo',
            'employee.department',
            'reviewer.personalInfo',
        ])
            ->when($employee, function ($query) use ($employee) {
                $query->where('employee_id', $employee->id)
                    ->orWhere('reviewer_id', $employee->id);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15);

        $scores = [];
        foreach ($reviews as $review) {
            $score = KpiMonthlyScore::where('employee_id', $review->employee_id)
                ->where('year', $review->year)
                ->where('month', $review->month)
                ->first();
            if ($score) {
                $scores[$review->employee_id.'_'.$review->year.'_'.$review->month] = $score;
            }
        }

        return view('kpi::reviews.index', compact('reviews', 'scores'));
    }

    public function create(?Employee $employee = null)
    {
        $employees = Employee::with('personalInfo', 'department')
            ->active()
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->full_name ?: ($emp->personalInfo?->full_name ?? 'N/A'),
                    'code' => $emp->employee_code,
                    'department' => $emp->department?->name ?? '',
                ];
            });

        return view('kpi::reviews.create', compact('employee', 'employees'));
    }

    public function store(StoreReviewRequest $request)
    {
        $result = $this->reviewService->saveReview($request->validated());

        if ($request->ajax()) {
            if ($result['status'] === 'success') {
                return response()->json($result);
            }

            return response()->json($result, 422);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.reviews.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function edit(int $id)
    {
        $review = KpiMonthlyReview::with('employee.personalInfo')->findOrFail($id);

        if ($review->status !== 'Draft') {
            return redirect()->route('kpi.reviews.index')
                ->with('error', 'Only draft reviews can be edited.');
        }

        return view('kpi::reviews.edit', compact('review'));
    }

    public function update(StoreReviewRequest $request, int $id)
    {
        $result = $this->reviewService->saveReview($request->validated(), $id);

        if ($request->ajax()) {
            if ($result['status'] === 'success') {
                return response()->json($result);
            }

            return response()->json($result, 422);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.reviews.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function show(int $id)
    {
        $review = KpiMonthlyReview::with([
            'employee.personalInfo',
            'employee.department',
            'reviewer.personalInfo',
        ])->findOrFail($id);

        $score = KpiMonthlyScore::where('employee_id', $review->employee_id)
            ->where('year', $review->year)
            ->where('month', $review->month)
            ->first();

        return view('kpi::reviews.show', compact('review', 'score'));
    }

    public function submit(int $id)
    {
        $result = $this->reviewService->submitReview($id);

        if (request()->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.reviews.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function approve(int $id)
    {
        $result = $this->reviewService->approveReview($id);

        if (request()->ajax()) {
            return response()->json($result);
        }

        if ($result['status'] === 'success') {
            return redirect()->route('kpi.reviews.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
