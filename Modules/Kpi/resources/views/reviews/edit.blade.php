<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h1 class="text-xl font-bold text-gray-800">Edit Monthly KPI Review</h1>
                <p class="text-sm text-gray-500 mt-1">Update review for {{ $review->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
            </div>

            <form id="editReviewForm" class="p-6">
                <input type="hidden" name="id" value="{{ $review->id }}">

                {{-- Employee Info (Read-only) --}}
                <div class="mb-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                            {{ $review->employee?->personalInfo?->full_name ? strtoupper(substr($review->employee->personalInfo->full_name, 0, 2)) : 'NA' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $review->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $review->employee?->employee_code ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $review->year }} - {{ \Carbon\Carbon::createFromDate($review->year, $review->month, 1)->format('F') }}</p>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                {{-- Behavior Section --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <input type="hidden" name="give_behavior" value="0">
                        <input type="checkbox" id="give_behavior" name="give_behavior" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ $review->give_behavior ? 'checked' : '' }}>
                        <label for="give_behavior" class="text-sm font-semibold text-gray-700 cursor-pointer">Evaluate Behavior</label>
                    </div>
                    <div id="behaviorFields" class="bg-gray-50 rounded-lg p-4 border border-gray-200 {{ $review->give_behavior ? '' : 'hidden' }}">
                        <div class="mb-4">
                            <x-form-input label="Behavior Score (0-10)" name="behavior_score" id="behavior_score" type="number" step="0.1" min="0" max="10" placeholder="0.0" value="{{ $review->behavior_score ?? '' }}" />
                        </div>
                        <div>
                            <x-form-textarea label="Behavior Remarks" name="behavior_remarks" id="behavior_remarks" placeholder="Add remarks about employee behavior..." rows="2">{{ $review->behavior_remarks ?? '' }}</x-form-textarea>
                        </div>
                    </div>
                </div>

                {{-- Bonus Section --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <input type="hidden" name="give_bonus" value="0">
                        <input type="checkbox" id="give_bonus" name="give_bonus" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ $review->give_bonus ? 'checked' : '' }}>
                        <label for="give_bonus" class="text-sm font-semibold text-gray-700 cursor-pointer">Award Bonus</label>
                    </div>
                    <div id="bonusFields" class="bg-gray-50 rounded-lg p-4 border border-gray-200 {{ $review->give_bonus ? '' : 'hidden' }}">
                        <div class="mb-4">
                            <x-form-input label="Bonus Score (0-10)" name="bonus_score" id="bonus_score" type="number" step="0.1" min="0" max="10" placeholder="0.0" value="{{ $review->bonus_score ?? '' }}" />
                        </div>
                        <div>
                            <x-form-textarea label="Bonus Remarks" name="bonus_remarks" id="bonus_remarks" placeholder="Add remarks about bonus..." rows="2">{{ $review->bonus_remarks ?? '' }}</x-form-textarea>
                        </div>
                    </div>
                </div>

                {{-- Penalty Section --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <input type="hidden" name="give_penalty" value="0">
                        <input type="checkbox" id="give_penalty" name="give_penalty" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ $review->give_penalty ? 'checked' : '' }}>
                        <label for="give_penalty" class="text-sm font-semibold text-gray-700 cursor-pointer">Apply Penalty</label>
                    </div>
                    <div id="penaltyFields" class="bg-gray-50 rounded-lg p-4 border border-gray-200 {{ $review->give_penalty ? '' : 'hidden' }}">
                        <div class="mb-4">
                            <x-form-input label="Penalty Score (0-10)" name="penalty_score" id="penalty_score" type="number" step="0.1" min="0" max="10" placeholder="0.0" value="{{ $review->penalty_score ?? '' }}" />
                        </div>
                        <div>
                            <x-form-textarea label="Penalty Remarks" name="penalty_remarks" id="penalty_remarks" placeholder="Add remarks about penalty..." rows="2">{{ $review->penalty_remarks ?? '' }}</x-form-textarea>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Update Review
                    </button>
                    <a href="{{ route('kpi.reviews.index') }}"
                        class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize field visibility
                if ($('#give_behavior').is(':checked')) {
                    $('#behaviorFields').show();
                }
                if ($('#give_bonus').is(':checked')) {
                    $('#bonusFields').show();
                }
                if ($('#give_penalty').is(':checked')) {
                    $('#penaltyFields').show();
                }

                // Toggle behavior fields
                $('#give_behavior').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#behaviorFields').show();
                        $('#behavior_score').prop('required', true);
                    } else {
                        $('#behaviorFields').hide();
                        $('#behavior_score').prop('required', false);
                    }
                });

                // Toggle bonus fields
                $('#give_bonus').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#bonusFields').show();
                        $('#bonus_score').prop('required', true);
                    } else {
                        $('#bonusFields').hide();
                        $('#bonus_score').prop('required', false);
                    }
                });

                // Toggle penalty fields
                $('#give_penalty').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#penaltyFields').show();
                        $('#penalty_score').prop('required', true);
                    } else {
                        $('#penaltyFields').hide();
                        $('#penalty_score').prop('required', false);
                    }
                });

                // Form submission
                $('#editReviewForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = $(this).serialize();
                    formData += '&_method=PUT';

                    $.ajax({
                        url: "{{ route('kpi.reviews.update', $review->id) }}",
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message || 'Review updated successfully',
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
                                Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Server error occurred';
                            if (xhr.responseJSON?.errors) {
                                errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            } else if (xhr.responseJSON?.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorMsg
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>