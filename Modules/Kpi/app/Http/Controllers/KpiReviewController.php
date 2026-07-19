<?php

namespace Modules\Kpi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Kpi\Services\KpiReviewService;
use Modules\Kpi\Models\KpiMonthlyReview;
use Modules\Kpi\Models\KpiMonthlyScore;
use Modules\Employee\Models\Employee;

class KpiReviewController extends Controller
{
    protected KpiReviewService $reviewService;

    public function __construct(KpiReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display a listing of reviews.
     */
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $reviews = KpiMonthlyReview::with([
            'employee.personalInfo',
            'employee.department',
            'reviewer.personalInfo'
        ])
            ->when($employee, function ($query) use ($employee) {
                $query->where('employee_id', $employee->id)
                    ->orWhere('reviewer_id', $employee->id);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15);

        // Fetch matching KPI monthly scores for each review to display overall performance
        $scores = [];
        foreach ($reviews as $review) {
            $score = KpiMonthlyScore::where('employee_id', $review->employee_id)
                ->where('year', $review->year)
                ->where('month', $review->month)
                ->first();
            if ($score) {
                $scores[$review->employee_id . '_' . $review->year . '_' . $review->month] = $score;
            }
        }

        return view('kpi::reviews.index', compact('reviews', 'scores'));
    }

    /**
     * Show the form for creating a new review.
     */
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

    /**
     * Store a newly created review.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'give_behavior' => 'boolean',
            'behavior_score' => 'nullable|numeric|min:0|max:10',
            'behavior_remarks' => 'nullable|string|max:500',
            'give_bonus' => 'boolean',
            'bonus_score' => 'nullable|numeric|min:0|max:10',
            'bonus_remarks' => 'nullable|string|max:500',
            'give_penalty' => 'boolean',
            'penalty_score' => 'nullable|numeric|min:0|max:10',
            'penalty_remarks' => 'nullable|string|max:500',
        ]);

        $result = $this->reviewService->saveReview($validated);

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

    /**
     * Show the form for editing the specified review.
     */
    public function edit(int $id)
    {
        $review = KpiMonthlyReview::with('employee.personalInfo')->findOrFail($id);

        if ($review->status !== 'Draft') {
            return redirect()->route('kpi.reviews.index')
                ->with('error', 'Only draft reviews can be edited.');
        }

        return view('kpi::reviews.edit', compact('review'));
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'give_behavior' => 'boolean',
            'behavior_score' => 'nullable|numeric|min:0|max:10',
            'behavior_remarks' => 'nullable|string|max:500',
            'give_bonus' => 'boolean',
            'bonus_score' => 'nullable|numeric|min:0|max:10',
            'bonus_remarks' => 'nullable|string|max:500',
            'give_penalty' => 'boolean',
            'penalty_score' => 'nullable|numeric|min:0|max:10',
            'penalty_remarks' => 'nullable|string|max:500',
        ]);

        $result = $this->reviewService->saveReview($validated, $id);

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

    /**
     * Display the specified review.
     */
    public function show(int $id)
    {
        $review = KpiMonthlyReview::with([
            'employee.personalInfo',
            'employee.department',
            'reviewer.personalInfo'
        ])->findOrFail($id);

        // Fetch the matching monthly score for overall performance display
        $score = KpiMonthlyScore::where('employee_id', $review->employee_id)
            ->where('year', $review->year)
            ->where('month', $review->month)
            ->first();

        return view('kpi::reviews.show', compact('review', 'score'));
    }

    /**
     * Submit review for approval.
     */
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

    /**
     * Approve review.
     */
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