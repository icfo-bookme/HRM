<x-app-layout>
    <div class="p-4">
        {{-- FILTER SECTION --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Status" id="filter_status" class="dt-filter-kpiReviewTable">
                    <option value="">All Status</option>
                    <option value="Draft">Draft</option>
                    <option value="Submitted">Submitted</option>
                    <option value="Approved">Approved</option>
                </x-form-select>
            </div>

            {{-- Year Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-input label="Year" name="filter_year" id="filter_year" type="number" min="2020" max="2099" placeholder="All Years" class="dt-filter-kpiReviewTable" />
            </div>

            {{-- Month Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Month" id="filter_month" class="dt-filter-kpiReviewTable">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </x-form-select>
            </div>

            {{-- Reset Button --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800
                   rounded-lg transition active:scale-95">
                    Reset
                </button>
            </div>
        </div>

        {{-- REVIEWS TABLE --}}
        <div class="bg-white min-w-full shadow-md rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/50 gap-4">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-clipboard-check text-blue-600 text-lg"></i>
                    <span class="font-bold text-gray-800 tracking-tight text-base">Monthly KPI Reviews</span>
                </div>
                <a href="{{ route('kpi.reviews.create', auth()->user()->employee?->id) }}"
                    class="bg-blue-900 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all duration-200 text-sm font-medium whitespace-nowrap active:scale-95">
                    <i class="fa fa-plus-circle"></i> New Review
                </a>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table id="kpiReviewTable" class="w-full border-collapse rounded-lg text-sm text-gray-700">
                        <thead>
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">SL</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Employee</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Period</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Overall Score</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Rating</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Reviewer</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3.5">{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                                {{ $review->employee?->personalInfo?->full_name ? strtoupper(substr($review->employee->personalInfo->full_name, 0, 2)) : 'NA' }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $review->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500">{{ $review->employee?->employee_code ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-medium">{{ $review->year }}</span> -
                                        <span class="font-medium">{{ \Carbon\Carbon::createFromDate($review->year, $review->month, 1)->format('M') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $scoreKey = $review->employee_id . '_' . $review->year . '_' . $review->month;
                                            $overallPercentage = $scores[$scoreKey]->overall_percentage ?? null;
                                        @endphp
                                        @if($overallPercentage !== null)
                                            <span class="font-bold {{ $overallPercentage >= 80 ? 'text-green-600' : ($overallPercentage >= 60 ? 'text-blue-600' : 'text-orange-600') }}">
                                                {{ number_format($overallPercentage, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $scoreKey = $review->employee_id . '_' . $review->year . '_' . $review->month;
                                            $rating = $scores[$scoreKey]->rating ?? null;
                                            $ratingColors = [
                                                'A+' => 'bg-green-100 text-green-700',
                                                'A' => 'bg-green-100 text-green-700',
                                                'B+' => 'bg-blue-100 text-blue-700',
                                                'B' => 'bg-blue-100 text-blue-700',
                                                'C' => 'bg-yellow-100 text-yellow-700',
                                                'D' => 'bg-orange-100 text-orange-700',
                                            ];
                                            $ratingColor = $ratingColors[$rating] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        @if($rating)
                                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $ratingColor }}">
                                                {{ $rating }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
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
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600">
                                        {{ $review->reviewer?->personalInfo?->full_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('kpi.reviews.show', $review->id) }}"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($review->status === 'Draft')
                                                <a href="{{ route('kpi.reviews.edit', $review->id) }}"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="submitReview({{ $review->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200"
                                                    title="Submit for Approval">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            @endif
                                            @if($review->status === 'Submitted' && auth()->user()->employee?->id !== $review->reviewer_id)
                                                <button onclick="approveReview({{ $review->id }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200"
                                                    title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No reviews found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($reviews->hasPages())
                    <div class="mt-4">
                        {{ $reviews->links() }}
                    </div>
                @endif
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
                                    location.reload();
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
                                    location.reload();
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

            $('#resetFilters').on('click', function() {
                $('#filter_status').val('');
                $('#filter_year').val('');
                $('#filter_month').val('');
                // Reload page for non-AJAX table
                location.reload();
            });
        </script>
    @endpush
</x-app-layout>