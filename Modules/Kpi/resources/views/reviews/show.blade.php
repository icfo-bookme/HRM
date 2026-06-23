<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">KPI Review Details</h1>
                    <p class="text-sm text-gray-500 mt-1">Review ID: #{{ $review->id }}</p>
                </div>
                <div class="flex gap-2">
                    @if($review->status === 'Draft')
                        <a href="{{ route('kpi.reviews.edit', $review->id) }}"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            <i class="fas fa-edit mr-1"></i> Edit Review
                        </a>
                    @endif
                    <a href="{{ route('kpi.reviews.index') }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            {{-- REVIEW CONTENT --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Employee Info --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Employee Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                    {{ $review->employee?->personalInfo?->full_name ? strtoupper(substr($review->employee->personalInfo->full_name, 0, 2)) : 'NA' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $review->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $review->employee?->employee_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                {{ $review->employee?->department?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- Review Period --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Review Period</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Year:</span>
                                <span class="text-sm font-bold text-gray-800">{{ $review->year }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Month:</span>
                                <span class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::createFromDate($review->year, $review->month, 1)->format('F') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium">
                                    @php
                                        $statusColors = [
                                            'Draft' => 'bg-gray-100 text-gray-600',
                                            'Submitted' => 'bg-yellow-100 text-yellow-700',
                                            'Approved' => 'bg-green-100 text-green-700',
                                        ];
                                        $statusColor = $statusColors[$review->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                        {{ $review->status }}
                                    </span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Reviewer:</span>
                                <span class="text-sm font-medium text-gray-800">{{ $review->reviewer?->personalInfo?->full_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Overall Score --}}
                @if($score)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Overall Performance</h3>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Overall Score</p>
                                <p class="text-3xl font-bold {{ $score->overall_percentage >= 80 ? 'text-green-600' : ($score->overall_percentage >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                    {{ number_format($score->overall_percentage, 1) }}%
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 mb-1">Rating</p>
                                @php
                                    $ratingColors = [
                                        'A+' => 'bg-green-100 text-green-700',
                                        'A' => 'bg-green-100 text-green-700',
                                        'B+' => 'bg-blue-100 text-blue-700',
                                        'B' => 'bg-blue-100 text-blue-700',
                                        'C' => 'bg-yellow-100 text-yellow-700',
                                        'D' => 'bg-orange-100 text-orange-700',
                                    ];
                                    $ratingColor = $ratingColors[$score->rating] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="px-3 py-1 text-lg font-bold rounded-full {{ $ratingColor }}">
                                    {{ $score->rating ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Detailed Scores from Review --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Behavior --}}
                    @if($review->give_behavior)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Behavior</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Score:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format((float)($review->behavior_score ?? 0), 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Percentage:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $review->behavior_score ? number_format(($review->behavior_score / 10) * 100, 1) : 0 }}%</span>
                                </div>
                                @if($review->behavior_remarks)
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                        <p class="text-xs text-gray-600">Remarks:</p>
                                        <p class="text-xs text-gray-800 mt-1">{{ $review->behavior_remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Bonus --}}
                    @if($review->give_bonus)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Bonus</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Score:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format((float)($review->bonus_score ?? 0), 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Percentage:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $review->bonus_score ? number_format(($review->bonus_score / 10) * 100, 1) : 0 }}%</span>
                                </div>
                                @if($review->bonus_remarks)
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                        <p class="text-xs text-gray-600">Remarks:</p>
                                        <p class="text-xs text-gray-800 mt-1">{{ $review->bonus_remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Penalty --}}
                    @if($review->give_penalty)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Penalty</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Score:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ number_format((float)($review->penalty_score ?? 0), 1) }}/10</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Percentage:</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $review->penalty_score ? number_format(($review->penalty_score / 10) * 100, 1) : 0 }}%</span>
                                </div>
                                @if($review->penalty_remarks)
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                        <p class="text-xs text-gray-600">Remarks:</p>
                                        <p class="text-xs text-gray-800 mt-1">{{ $review->penalty_remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(!$review->give_behavior && !$review->give_bonus && !$review->give_penalty)
                        <div class="col-span-3 text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>No optional scores have been given for this review.</p>
                            <p class="text-sm mt-1">Behavior, Bonus, and Penalty scores are manager-defined.</p>
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex gap-3 pt-4 border-t border-gray-200">
                    @if($review->status === 'Draft')
                        <button onclick="submitReview({{ $review->id }})"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition">
                            <i class="fas fa-paper-plane mr-1"></i> Submit for Approval
                        </button>
                    @endif
                    @if($review->status === 'Submitted' && auth()->user()->employee?->id !== $review->reviewer_id)
                        <button onclick="approveReview({{ $review->id }})"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                            <i class="fas fa-check mr-1"></i> Approve Review
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function submitReview(id) {
                Swal.fire({
                    title: 'Submit Review?',
                    text: "This will submit the review for approval.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kpi.reviews.submit', ':id') }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Toastify({
                                        text: res.message || 'Review submitted successfully',
                                        duration: 3000,
                                        gravity: "bottom",
                                        position: "right",
                                        style: {
                                            background: "linear-gradient(135deg, #3b82f6, #60a5fa)"
                                        },
                                    }).showToast();
                                    setTimeout(() => {
                                        window.location.href = "{{ route('kpi.reviews.index') }}";
                                    }, 1000);
                                } else {
                                    Swal.fire('Error', res.message || 'Failed to submit review.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Server communication error.', 'error');
                            }
                        });
                    }
                });
            }

            function approveReview(id) {
                Swal.fire({
                    title: 'Approve Review?',
                    text: "This will approve the review and calculate the KPI score.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kpi.reviews.approve', ':id') }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Toastify({
                                        text: res.message || 'Review approved successfully',
                                        duration: 3000,
                                        gravity: "bottom",
                                        position: "right",
                                        style: {
                                            background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                        },
                                    }).showToast();
                                    setTimeout(() => {
                                        window.location.href = "{{ route('kpi.reviews.index') }}";
                                    }, 1000);
                                } else {
                                    Swal.fire('Error', res.message || 'Failed to approve review.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Server communication error.', 'error');
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>