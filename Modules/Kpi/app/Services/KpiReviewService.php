<?php

namespace Modules\Kpi\Services;

use Illuminate\Support\Facades\DB;
use Modules\Kpi\Models\KpiMonthlyReview;
use Modules\Kpi\Models\KpiMonthlyScore;
use Modules\Employee\Models\Employee;

class KpiReviewService
{
    /**
     * Create or update a monthly review
     */
    public function saveReview(array $data, ?int $reviewId = null): array
    {
        try {
            return DB::transaction(function () use ($data, $reviewId) {
                $data['reviewer_id'] = auth()->id();

                if ($reviewId) {
                    $review = KpiMonthlyReview::findOrFail($reviewId);
                    $review->update($data);
                    $message = 'Monthly review updated successfully.';
                } else {
                    $review = KpiMonthlyReview::create($data);
                    $message = 'Monthly review created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'review' => $review->fresh()->load(['employee.personalInfo', 'reviewer.personalInfo']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to save review: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Submit review for approval
     */
    public function submitReview(int $id): array
    {
        try {
            $review = KpiMonthlyReview::findOrFail($id);

            if ($review->status !== 'Draft') {
                return ['status' => 'error', 'message' => 'Only draft reviews can be submitted.'];
            }

            $review->update(['status' => 'Submitted']);

            return [
                'status' => 'success',
                'message' => 'Review submitted successfully.',
                'review' => $review->fresh(),
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to submit review: ' . $e->getMessage()];
        }
    }

    /**
     * Approve review
     */
    public function approveReview(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $review = KpiMonthlyReview::findOrFail($id);

                if ($review->status !== 'Submitted') {
                    return ['status' => 'error', 'message' => 'Only submitted reviews can be approved.'];
                }

                $review->update(['status' => 'Approved']);

                // Auto-calculate monthly score when review is approved
                $monthlyService = app(KpiMonthlyService::class);
                $monthlyService->calculateMonthlyScore(
                    $review->employee_id,
                    $review->year,
                    $review->month
                );

                return [
                    'status' => 'success',
                    'message' => 'Review approved and KPI score calculated.',
                    'review' => $review->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to approve review: ' . $e->getMessage()];
        }
    }

    /**
     * Get reviews pending action for a manager
     */
    public function getPendingReviews(int $managerId): array
    {
        $reviews = KpiMonthlyReview::with(['employee.personalInfo', 'employee.department'])
            ->where('reviewer_id', $managerId)
            ->where('status', 'Draft')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return [
            'status' => 'success',
            'data' => $reviews,
            'count' => $reviews->count(),
        ];
    }

    /**
     * Get review history for an employee
     */
    public function getEmployeeReviewHistory(int $employeeId): array
    {
        $reviews = KpiMonthlyReview::with(['reviewer.personalInfo'])
            ->where('employee_id', $employeeId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return [
            'status' => 'success',
            'data' => $reviews,
        ];
    }
}
