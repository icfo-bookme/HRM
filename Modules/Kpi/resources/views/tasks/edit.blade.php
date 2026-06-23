<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h1 class="text-xl font-bold text-gray-800">Edit KPI Task</h1>
                <p class="text-sm text-gray-500 mt-1">Update task details for {{ $task->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
            </div>

            <form id="editKpiTaskForm" class="p-6">
                <input type="hidden" name="id" value="{{ $task->id }}">

                {{-- Employee Info (Read-only) --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Employee</label>
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <p class="text-sm text-gray-800">{{ $task->employee?->employee_code ?? 'N/A' }} - {{ $task->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
                    </div>
                    <input type="hidden" name="employee_id" value="{{ $task->employee_id }}">
                </div>

                {{-- Title --}}
                <div class="mb-4">
                    <x-form-input label="Task Title" name="title" id="title" placeholder="Enter task title" required value="{{ $task->title }}" />
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <x-form-textarea label="Description" name="description" id="description"
                        placeholder="Task description (optional)" rows="3">{{ $task->description }}</x-form-textarea>
                </div>

                {{-- Target Score & Priority --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <x-form-input label="Target Score" name="target_score" id="target_score" type="number" step="0.01" min="0.01" placeholder="0.00" required value="{{ $task->target_score }}" />
                    <x-form-select label="Priority" name="priority" id="priority" required>
                        <option value="Low" {{ $task->priority == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ $task->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ $task->priority == 'High' ? 'selected' : '' }}>High</option>
                        <option value="Critical" {{ $task->priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                    </x-form-select>
                </div>

                {{-- Deadline & Status --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <x-form-input label="Deadline" name="deadline" id="deadline" type="date" value="{{ $task->deadline?->format('Y-m-d') }}" />
                    <x-form-select label="Status" name="status" id="status" required>
                        <option value="Pending" {{ $task->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ $task->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    </x-form-select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-900 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="fas fa-save mr-1"></i> Update Task
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
                $('#editKpiTaskForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = $(this).serialize();
                    formData += '&_method=PUT';

                    $.ajax({
                        url: "{{ route('kpi.tasks.update', $task->id) }}",
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Task updated successfully',
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