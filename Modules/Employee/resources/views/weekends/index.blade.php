<x-app-layout>
    <div class="p-4">

        {{-- Info Box --}}
        <div class="mb-5 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-blue-800">Employee Weekend Setup</p>
                <p class="text-xs text-blue-600 mt-1">Set weekend days for each employee. These will be used by payroll to calculate working days, late deductions, overtime, half-day and absent penalties correctly based on each employee's weekly off schedule.</p>
                <p class="text-xs text-blue-500 mt-1">
                    <strong>Day Mapping:</strong> Sun=0, Mon=1, Tue=2, Wed=3, Thu=4, Fri=5, Sat=6
                </p>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="mb-5">
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input 
                    type="text" 
                    id="employeeSearch" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Search by employee name or code..." 
                    class="block w-full pl-9 pr-10 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button 
                        type="button" 
                        id="clearSearch" 
                        class="text-gray-400 hover:text-gray-600 focus:outline-none {{ empty($search) ? 'hidden' : '' }}"
                    >
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Employee Weekend Cards Container --}}
        <div id="employeeCardsContainer">
            @include('employee::weekends.partials.employee-cards', ['employees' => $employees])
        </div>

        {{-- Pagination Container --}}
        <div id="paginationContainer">
            @include('employee::weekends.partials.pagination', ['employees' => $employees])
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Search functionality
            const searchInput = $('#employeeSearch');
            const clearBtn = $('#clearSearch');
            let searchTimeout;

            // Real-time search with debounce
            searchInput.on('input', function() {
                const query = $(this).val();
                
                // Show/hide clear button
                if (query.length > 0) {
                    clearBtn.removeClass('hidden');
                } else {
                    clearBtn.addClass('hidden');
                }

                // Debounce search
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    loadEmployees(1, query);
                }, 300);
            });

            // Clear search
            clearBtn.on('click', function() {
                searchInput.val('');
                $(this).addClass('hidden');
                loadEmployees(1, '');
            });

            // Pagination click handler
            $(document).on('click', '.pagination-btn', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                const query = searchInput.val();
                loadEmployees(page, query);
            });

            // Load employees via AJAX
            function loadEmployees(page, searchQuery) {
                const params = {
                    page: page,
                    search: searchQuery,
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: "{{ route('employee.weekends.index') }}",
                    type: 'GET',
                    data: params,
                    beforeSend: function() {
                        $('#employeeCardsContainer').addClass('opacity-50');
                    },
                    success: function(res) {
                        $('#employeeCardsContainer').html(res.html);
                        $('#paginationContainer').html(res.pagination);
                        $('#employeeCardsContainer').removeClass('opacity-50');
                        
                        // Re-bind form events for new elements
                        bindFormEvents();
                    },
                    error: function(xhr) {
                        $('#employeeCardsContainer').removeClass('opacity-50');
                        Swal.fire('Error', 'Failed to load employees', 'error');
                    }
                });
            }

            // Bind form submission events
            function bindFormEvents() {
                $('.weekend-form').off('submit').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const btn = form.find('.save-weekend-btn');
                    const btnText = btn.find('span');
                    const employeeId = form.data('employee');

                    // Collect checked days
                    const checked = [];
                    form.find('.weekend-day-cb:checked').each(function() {
                        checked.push($(this).val());
                    });

                    btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
                    btnText.text('Saving...');

                    $.ajax({
                        url: "{{ route('employee.weekends.store') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: employeeId,
                            weekend_days: checked,
                        },
                        success: function(res) {
                            btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                            btnText.text('Save');

                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message || 'Weekend updated successfully',
                                    duration: 2000,
                                    gravity: 'bottom',
                                    position: 'right',
                                    style: { background: 'linear-gradient(135deg, #16a34a, #4ade80)' },
                                }).showToast();
                            } else {
                                Swal.fire('Error', res.message || 'Failed to save', 'error');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                            btnText.text('Save');
                            Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
                        }
                    });
                });
            }

            // Initial bind
            bindFormEvents();
        });
    </script>
    @endpush
</x-app-layout>