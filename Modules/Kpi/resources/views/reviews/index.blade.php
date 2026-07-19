<x-app-layout>
    <div class="p-4">
        {{-- FILTERS --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden mb-4">
            <div class="flex flex-wrap items-center gap-4 px-6 py-4">
                <div class="flex flex-col w-full md:w-1/3">
                    <x-form-select label="Status" id="filter_status" class="dt-filter-kpiReviewTable">
                        <option value="">All Status</option>
                        <option value="Draft">Draft</option>
                        <option value="Submitted">Submitted</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </x-form-select>
                </div>
                <div class="flex flex-col w-full md:w-1/3">
                    <x-form-input label="Year" name="filter_year" id="filter_year" type="number" min="2020" max="2099" placeholder="All Years" class="dt-filter-kpiReviewTable" />
                </div>
                <div class="flex flex-col w-full md:w-1/3">
                    <x-form-select label="Month" id="filter_month" class="dt-filter-kpiReviewTable">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::createFromDate(2024, $m, 1)->format('F') }}</option>
                        @endfor
                    </x-form-select>
                </div>
            </div>
        </div>

        {{-- MAIN CARD --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-blue-600 text-lg"></i>
                    <span class="font-bold text-gray-800 tracking-tight text-base">Monthly KPI Reviews</span>
                </div>
                <a href="{{ route('kpi.reviews.create') }}"
                    class="bg-blue-900 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all duration-200 text-sm font-medium whitespace-nowrap active:scale-95">
                    <i class="fa-solid fa-plus-circle"></i> Add New Review
                </a>
            </div>

            <div class="overflow-x-auto">
                <table id="kpiReviewTable" class="w-full border-collapse rounded-lg text-sm text-gray-700">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                            <th class="px-5 py-3.5 text-left font-semibold">#</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Employee</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Period</th>
                            <th class="px-5 py-3.5 text-center font-semibold">KPI Score</th>
                            <th class="px-5 py-3.5 text-center font-semibold">Rating</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Status</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Reviewer</th>
                            <th class="px-5 py-3.5 text-center font-semibold">Action</th>
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
                                <td class="px-5 py-3.5 text-center font-semibold">
                                    @php
                                        $scoreKey = $review->employee_id . '_' . $review->year . '_' . $review->month;
                                        $overallPercentage = $scores[$scoreKey]->overall_percentage ?? null;
                                    @endphp
                                    @if($overallPercentage !== null)
                                        <span class="text-blue-700">{{ number_format($overallPercentage, 1) }}%</span>
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @php
                                        $scoreKey = $review->employee_id . '_' . $review->year . '_' . $review->month;
                                        $rating = $scores[$scoreKey]->rating ?? null;
                                    @endphp
                                    @if($rating)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $rating === 'A' ? 'bg-green-100 text-green-700' : ($rating === 'B' ? 'bg-blue-100 text-blue-700' : ($rating === 'C' ? 'bg-yellow-100 text-yellow-700' : ($rating === 'D' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'))) }}">
                                            {{ $rating }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusColors = [
                                            'Draft' => 'bg-gray-100 text-gray-600',
                                            'Submitted' => 'bg-yellow-100 text-yellow-700',
                                            'Approved' => 'bg-green-100 text-green-700',
                                            'Rejected' => 'bg-red-100 text-red-700',
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
                                <td class="px-5 py-3.5">
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
                                                title="Submit">
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
                                <td colspan="8" class="px-5 py-8 text-center">
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

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Filter handlers
            $('#filter_status, #filter_month').on('change', function() {
                filterTable();
            });
            $('#filter_year').on('input', function() {
                filterTable();
            });

            function filterTable() {
                var status = $('#filter_status').val();
                var year = $('#filter_year').val();
                var month = $('#filter_month').val();

                $('#kpiReviewTable').find('tbody tr').each(function() {
                    var row = $(this);
                    var show = true;

                    if (status && !row.find('td:eq(5)').text().trim().includes(status)) {
                        show = false;
                    }
                    if (year) {
                        var period = row.find('td:eq(2)').text().trim();
                        if (!period.includes(year)) {
                            show = false;
                        }
                    }
                    if (month) {
                        var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        var monthName = monthNames[parseInt(month) - 1];
                        var period = row.find('td:eq(2)').text().trim();
                        if (!period.includes(monthName)) {
                            show = false;
                        }
                    }

                    row.toggle(show);
                });
            }
        });

        function submitReview(id) {
            Swal.fire({
                title: 'Submit Review?',
                text: "This will submit the review for approval.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, submit'
            }).then((r) => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: "{{ route('kpi.reviews.submit', ':id') }}".replace(':id', id),
                        type: 'PUT',
                        success: function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #d97706, #f59e0b)" }
                                }).showToast();
                                setTimeout(() => { location.reload(); }, 1000);
                            } else {
                                Swal.fire('Error', res.message || 'Failed to submit review.', 'error');
                            }
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
                confirmButtonText: 'Yes, approve'
            }).then((r) => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: "{{ route('kpi.reviews.approve', ':id') }}".replace(':id', id),
                        type: 'PUT',
                        success: function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" }
                                }).showToast();
                                setTimeout(() => { location.reload(); }, 1000);
                            } else {
                                Swal.fire('Error', res.message || 'Failed to approve review.', 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>