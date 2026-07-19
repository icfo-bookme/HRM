<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h1 class="text-xl font-bold text-gray-800">Create New KPI Task</h1>
                <p class="text-sm text-gray-500 mt-1">Assign a new KPI task to an employee</p>
            </div>

            <form id="createKpiTaskForm" class="p-6">
                {{-- Employee Select --}}
                <div class="mb-4">
                    <x-form-select label="Employee" name="employee_id" id="employee_id" placeholder="Select Employee" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->personalInfo?->full_name ?? 'N/A' }}</option>
                        @endforeach
                    </x-form-select>
                </div>

                {{-- Title --}}
                <div class="mb-4">
                    <x-form-input label="Task Title" name="title" id="title" placeholder="Enter task title" required />
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <x-form-textarea label="Description" name="description" id="description"
                        placeholder="Task description (optional)" rows="3" />
                </div>

                {{-- Target Score & Priority --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <x-form-input label="Target Score" name="target_score" id="target_score" type="number" step="0.01" min="0.01" placeholder="0.00" required />
                    <x-form-select label="Priority" name="priority" id="priority" required>
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </x-form-select>
                </div>

                {{-- Deadline --}}
                <div class="mb-4">
                    <x-form-input label="Deadline" name="deadline" id="deadline" type="date" />
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Create Task
                    </button>
                    <a href="{{ route('kpi.tasks.index') }}"
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
                $('#createKpiTaskForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = $(this).serialize();

                    $.ajax({
                        url: "{{ route('kpi.tasks.store') }}",
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Task created successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: {
                                        background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                    },
                                }).showToast();
                                setTimeout(() => {
                                    window.location.href = "{{ route('kpi.tasks.index') }}";
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