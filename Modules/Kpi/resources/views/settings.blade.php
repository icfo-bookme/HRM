<x-app-layout>
    <div class="p-4">
        {{-- PAGE HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">KPI Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage KPI categories, indicators, and weightages</p>
        </div>

        {{-- CATEGORIES SECTION --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800">KPI Categories</h2>
                <p class="text-sm text-gray-500 mt-1">Configure category weightages (total must equal 100%)</p>
            </div>

            <form id="categoriesForm" class="p-6">
                @csrf
                <div class="space-y-4">
                    @foreach($categories as $category)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-folder text-blue-500"></i>
                                    <span class="font-semibold text-gray-800">{{ $category->name }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $category->calculation_type }} | {{ $category->point_setting }}</p>
                            </div>
                            <div class="w-32">
                                <x-form-input label="Weight %" name="categories[{{ $category->id }}][weight_percentage]" 
                                    type="number" min="0" max="100" step="0.1" 
                                    value="{{ $category->weight_percentage }}" required />
                            </div>
                            <div class="w-24">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="categories[{{ $category->id }}][is_active]" 
                                        value="1" {{ $category->is_active ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                            <input type="hidden" name="categories[{{ $category->id }}][id]" value="{{ $category->id }}">
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-200">
                    <div>
                        <span class="text-sm text-gray-600">Total Weight: </span>
                        <span id="totalWeight" class="text-sm font-bold text-gray-800">0%</span>
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Save Categories
                    </button>
                </div>
            </form>
        </div>

        {{-- INDICATORS SECTION --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800">KPI Indicators</h2>
                <p class="text-sm text-gray-500 mt-1">Configure indicator settings and weightages</p>
            </div>

            <form id="indicatorsForm" class="p-6">
                @csrf
                <div class="space-y-6">
                    @foreach($categories as $category)
                        @if($category->indicators->count() > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <i class="fas fa-folder-open text-blue-500"></i>
                                    {{ $category->name }}
                                </h3>
                                <div class="space-y-3 ml-6">
                                    @foreach($category->indicators as $indicator)
                                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex-1">
                                                <span class="font-medium text-gray-800">{{ $indicator->name }}</span>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $indicator->count_behavior }} | Points: {{ $indicator->point_per_unit ?? $indicator->default_max_score ?? 'N/A' }}</p>
                                            </div>
                                            <div class="w-32">
                                                <x-form-input label="Weight %" name="indicators[{{ $indicator->id }}][weight_percentage]" 
                                                    type="number" min="0" max="100" step="0.1" 
                                                    value="{{ $indicator->weight_percentage }}" required />
                                            </div>
                                            <div class="w-32">
                                                <x-form-input label="Points/Unit" name="indicators[{{ $indicator->id }}][point_per_unit]" 
                                                    type="number" min="0" step="0.1" 
                                                    value="{{ $indicator->point_per_unit ?? '' }}" />
                                            </div>
                                            <div class="w-24">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" name="indicators[{{ $indicator->id }}][is_active]" 
                                                        value="1" {{ $indicator->is_active ? 'checked' : '' }}
                                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                    <span class="text-sm text-gray-700">Active</span>
                                                </label>
                                            </div>
                                            <input type="hidden" name="indicators[{{ $indicator->id }}][id]" value="{{ $indicator->id }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Save Indicators
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Calculate total weight
                function calculateTotalWeight() {
                    let total = 0;
                    $('input[name^="categories"][name$="[weight_percentage]"]').each(function() {
                        let value = parseFloat($(this).val()) || 0;
                        total += value;
                    });
                    $('#totalWeight').text(total.toFixed(1) + '%');
                    
                    if (total === 100) {
                        $('#totalWeight').removeClass('text-red-600').addClass('text-green-600');
                    } else {
                        $('#totalWeight').removeClass('text-green-600').addClass('text-red-600');
                    }
                }

                // Initial calculation
                calculateTotalWeight();

                // Recalculate on change
                $('input[name^="categories"][name$="[weight_percentage]"]').on('input', function() {
                    calculateTotalWeight();
                });

                // Categories form submission
                $('#categoriesForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    let formData = $(this).serialize();
                    
                    $.ajax({
                        url: "{{ route('kpi.settings.categories') }}",
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message || 'Categories updated successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: {
                                        background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                    },
                                }).showToast();
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

                // Indicators form submission
                $('#indicatorsForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    let formData = $(this).serialize();
                    
                    $.ajax({
                        url: "{{ route('kpi.settings.indicators') }}",
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message || 'Indicators updated successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: {
                                        background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                    },
                                }).showToast();
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