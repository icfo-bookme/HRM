<x-app-layout>
    <div class="p-4">
        <x-data-table id="salaryGradeTable" title="Salary Grade Management" icon="fa-solid fa-money-bill-wave" buttonId="btnAddSalaryGrade"
            buttonText="Add New Grade" :columns="[ 'Name', 'Min Salary', 'Max Salary', 'Currency', 'Status', 'Created At', 'Action']" :ajaxUrl="route('salarygrade.dataTable')" :dtColumns="[
               
                ['data' => 'name'],
                ['data' => 'min_salary'],
                ['data' => 'max_salary'],
                ['data' => 'currency'],
                ['data' => 'is_active'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    <x-drawer id="salarygrade-drawer" overlayId="salarygrade-overlay" title="Add New Salary Grade" submitOnClick="saveForm()">
        <form id="salarygradeForm">
            <input type="hidden" id="salary_grade_id" name="id">

            
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                {{-- <x-form-input label="Code" name="code" placeholder="Grade code" /> --}}
                <x-form-input label="Name" name="name" placeholder="Grade name"  required/>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-input label="Min Salary" name="min_salary" type="number" step="0.01" placeholder="0.00" required />
                <x-form-input label="Max Salary" name="max_salary" type="number" step="0.01" placeholder="0.00" required />
                <x-form-input label="Currency" name="currency" placeholder="USD" value="BDT" required />
            </div>

            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade" style="animation-delay: 200ms;">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Active</span>
                </label>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSaving = false;
            const csrfToken = '{{ csrf_token() }}';

            function openSalaryGradeDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Salary Grade');
                    $('#drawerButtonText').text('Update Grade');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Salary Grade');
                    $('#drawerButtonText').text('Save Grade');
                }
                openGlobalDrawer('salarygrade-drawer', 'salarygrade-overlay');
            }

            function resetForm() {
                $('#salarygradeForm')[0].reset();
                $('#salary_grade_id').val('');
                $('#is_active').prop('checked', true);
            }

            function salaryGradeEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching salary grade details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();

                let fetchUrl = "{{ route('salarygrade.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    if (res.status === 'success') {
                        let grade = res.salary_grade;
                        $('#salary_grade_id').val(grade.id);
                        // $('#company_id').val(grade.company_id);
                        // $('#code').val(grade.code);
                        $('#name').val(grade.name);
                        $('#min_salary').val(grade.min_salary);
                        $('#max_salary').val(grade.max_salary);
                        $('#currency').val(grade.currency);
                        $('#is_active').prop('checked', grade.is_active == 1);
                        Swal.close();
                        openSalaryGradeDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#salary_grade_id').val();
                let url = id ? "{{ route('salarygrade.update', ':id') }}".replace(':id', id) : "{{ route('salarygrade.store') }}";
                let formData = $('#salarygradeForm').serialize();

                if (id) formData += '&_method=PUT';
                formData += `&_token=${csrfToken}`;

                isSaving = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                Swal.fire({
                    title: 'Confirm',
                    text: id ? 'Update salary grade?' : 'Create salary grade?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        isSaving = false;
                        $('#drawerButtonText').text(id ? 'Update Grade' : 'Save Grade');
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        return;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');

                            if (res.status === 'success') {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: 'bottom',
                                    position: 'right',
                                    backgroundColor: 'linear-gradient(135deg, #16a34a, #4ade80)',
                                }).showToast();
                                closeGlobalDrawer('salarygrade-drawer', 'salarygrade-overlay');
                                $('#salaryGradeTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                                $('#drawerButtonText').text(id ? 'Update Grade' : 'Save Grade');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text(id ? 'Update Grade' : 'Save Grade');
                            let errorMsg = 'Server error occurred';
                            if (xhr.responseJSON?.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON?.errors) {
                                errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            function salaryGradeDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: 'This cannot be undone',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('salarygrade.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, {
                            _method: 'DELETE',
                            _token: csrfToken
                        }, function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Deleted', res.message, 'success');
                                $('#salaryGradeTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete salary grade.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('click', '#btnAddSalaryGrade', function(e) {
                    e.preventDefault();
                    openSalaryGradeDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>
