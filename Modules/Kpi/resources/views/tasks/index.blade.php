<x-app-layout>
    <div class="p-4">
        {{-- FILTER SECTION --}}
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Employee Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Employee" id="filter_employee" class="dt-filter-kpiTaskTable">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} -
                            {{ $employee->personalInfo?->full_name ?? 'N/A' }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Status" id="filter_status" class="dt-filter-kpiTaskTable">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Overdue">Overdue</option>
                </x-form-select>
            </div>

            {{-- Priority Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Priority" id="filter_priority" class="dt-filter-kpiTaskTable">
                    <option value="">All Priority</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
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

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="kpiTaskTable" title="KPI Task Management" icon="fa-solid fa-list-check"
            buttonId="btnAddKpiTask" buttonText="Add New Task" :columns="[
                'SL',
                'Employee',
                'Title',
                'Target Score',
                'Obtained Score',
                'Priority',
                'Deadline',
                'Status',
                'Created At',
                'Action',
            ]" :ajaxUrl="route('kpi.tasks.index')" :dtColumns="[
                [
                    'data' => 'DT_RowIndex',
                    'name' => 'DT_RowIndex',
                    'searchable' => false,
                    'orderable' => false,
                    'width' => '50px',
                ],
                ['data' => 'employee_id'],
                ['data' => 'title'],
                ['data' => 'target_score', 'width' => '100px'],
                ['data' => 'obtained_score', 'width' => '100px'],
                ['data' => 'priority', 'width' => '100px'],
                ['data' => 'deadline', 'width' => '110px'],
                ['data' => 'status', 'width' => '110px'],
                ['data' => 'assigned_date', 'width' => '110px'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false, 'width' => '150px'],
            ]"
            :filters="[
                'employee_id' => '#filter_employee',
                'status' => '#filter_status',
                'priority' => '#filter_priority',
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT FOR ADD/EDIT --}}
    <x-drawer id="kpi-task-drawer" overlayId="kpi-task-overlay" title="Add New KPI Task"
        submitOnClick="saveKpiTaskForm()">
        <form id="kpiTaskForm">
            <input type="hidden" name="id" id="kpi_task_id">

            {{-- Employee Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-select label="Employee" name="employee_id" id="employee_id" placeholder="Select Employee"
                    required>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->employee_code }} -
                            {{ $employee->personalInfo?->full_name ?? 'N/A' }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Title --}}
            <div class="mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Task Title" name="title" id="title" placeholder="Enter task title"
                    required />
            </div>

            {{-- Description --}}
            <div class="mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-textarea label="Description" name="description" id="description"
                    placeholder="Task description (optional)" rows="3" />
            </div>

            {{-- Target Score & Priority --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="Target Score" name="target_score" id="target_score" type="number" step="0.01"
                    min="0.01" placeholder="0.00" required />
                <x-form-select label="Priority" name="priority" id="priority" required>
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                </x-form-select>
            </div>

            {{-- Deadline --}}
            <div class="mb-4 animate-fade" style="animation-delay: 300ms;">
                <x-form-input label="Deadline" name="deadline" id="deadline" type="date" />
            </div>
        </form>
    </x-drawer>

    {{-- COMPLETE TASK MODAL --}}
    <x-modal name="completeTaskModal" title="Complete Task" size="md">
        <form id="completeTaskForm">
            <input type="hidden" name="task_id" id="complete_task_id">

            {{-- Obtained Score --}}
            <div class=" p-5">
                <x-form-input label="Obtained Score" name="obtained_score" id="obtained_score" type="number"
                    step="0.01" min="0" placeholder="0.00" required />
            </div>

            {{-- Completion Note --}}
            <div class="mb-4 p-5">
                <x-form-textarea label="Completion Note" name="completion_note" id="completion_note"
                    placeholder="Add completion notes (optional)" rows="3" />
            </div>
        </form>


        <button type="button" onclick="submitCompleteTask()"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
            <i class="fas fa-check mr-1"></i> Mark Complete
        </button>
        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'completeTaskModal' }))"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
            Cancel
        </button>

    </x-modal>

    @push('scripts')
        <script>
            let isSaving = false;

            function openKpiTaskDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update KPI Task');
                    $('#drawerButtonText').text('Update Task');
                } else {
                    resetKpiTaskForm();
                    $('#drawerTitle').text('Add New KPI Task');
                    $('#drawerButtonText').text('Save Task');
                }
                openGlobalDrawer('kpi-task-drawer', 'kpi-task-overlay');
            }

            function resetKpiTaskForm() {
                $('#kpiTaskForm')[0].reset();
                $('#kpi_task_id').val('');
                $('#priority').val('Medium');
            }

            function kpiTaskEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: `Fetching task details`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetKpiTaskForm();
                let fetchUrl = "{{ route('kpi.tasks.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let task = res.task;
                        $('#kpi_task_id').val(task.id);
                        $('select[name="employee_id"]').val(task.employee_id);
                        $('input[name="title"]').val(task.title);
                        $('textarea[name="description"]').val(task.description);
                        $('input[name="target_score"]').val(task.target_score);
                        $('select[name="priority"]').val(task.priority);
                        $('input[name="deadline"]').val(task.deadline);

                        openKpiTaskDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveKpiTaskForm() {
                if (isSaving) return;

                let id = $('#kpi_task_id').val();
                let url = id ? "{{ route('kpi.tasks.update', ':id') }}".replace(':id', id) :
                    "{{ route('kpi.tasks.store') }}";

                let formData = $('#kpiTaskForm').serialize();
                if (id) formData += '&_method=PUT';

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        console.log(res);
                        if (res.status == 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: {
                                    background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                },
                            }).showToast();
                            closeGlobalDrawer('kpi-task-drawer', 'kpi-task-overlay');
                            $('#kpiTaskTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Task' : 'Save Task');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Task' : 'Save Task');

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
            }

            function completeTask(id) {
                $('#complete_task_id').val(id);
                $('#obtained_score').val('');
                $('#completion_note').val('');
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'completeTaskModal'
                }));
            }

            function submitCompleteTask() {
                let taskId = $('#complete_task_id').val();
                let obtainedScore = $('#obtained_score').val();
                let completionNote = $('#completion_note').val();

                if (obtainedScore === '' || obtainedScore === null || obtainedScore === undefined || parseFloat(obtainedScore) <
                    0) {
                    Swal.fire('Validation Error', 'Please enter a valid obtained score.', 'error');
                    return;
                }

                $.ajax({
                    url: "{{ route('kpi.tasks.complete', ':id') }}".replace(':id', taskId),
                    type: 'PUT',
                    data: {
                        obtained_score: obtainedScore,
                        completion_note: completionNote,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        window.dispatchEvent(new CustomEvent('close-modal', {
                            detail: 'completeTaskModal'
                        }));
                        if (res.status === 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Task completed successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: {
                                    background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                },
                            }).showToast();
                            $('#kpiTaskTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Failed to complete task.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Server error occurred';
                        if (xhr.responseJSON?.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }

            function kpiTaskDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: 'Yes, delete it!'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('kpi.tasks.destroy', ':id') }}".replace(':id', id);

                        $.post(deleteUrl, {
                            _method: 'DELETE',
                            _token: "{{ csrf_token() }}"
                        }, function(res) {
                            if (res.status === 'success' || res.status === true) {
                                Toastify({
                                    text: res.message || 'Deleted successfully',
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    style: {
                                        background: "linear-gradient(135deg, #dc2626, #f87171)"
                                    },
                                }).showToast();
                                $('#kpiTaskTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            function viewKpiTask(id) {
                window.location.href = "{{ route('kpi.tasks.show', ':id') }}".replace(':id', id);
            }

            $('#resetFilters').on('click', function() {
                $('#filter_employee').val('');
                $('#filter_status').val('');
                $('#filter_priority').val('');

                $('.dt-filter-kpiTaskTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddKpiTask', function(e) {
                    e.preventDefault();
                    openKpiTaskDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>
