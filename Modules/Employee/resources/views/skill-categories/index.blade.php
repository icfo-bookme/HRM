<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/3">
                <x-form-select label="Status" id="filter_status" class="dt-filter-skillCategoryTable">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
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

        {{-- DATA-TABLE COMPONENT --}}
        <x-data-table id="skillCategoryTable" title="Skill Categories Management" icon="fa-solid fa-tags"
            buttonId="btnAddSkillCategory" buttonText="Add New Skill Category" :columns="[ 'Name', 'Description', 'Status', 'Action']" :ajaxUrl="route('skill-categories.dataTable')"
            :dtColumns="[
                ['data' => 'name'],
                ['data' => 'description', 'width' => '200px'],
                ['data' => 'is_active'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'is_active' => '#filter_status',
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="skill-category-drawer" overlayId="skill-category-overlay" title="Add New Skill Category"
        submitOnClick="saveForm()">
        <form id="skillCategoryForm">
            <input type="hidden" name="id" id="category_id">

            {{-- Name --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-input label="Name" name="name" id="name" placeholder="Skill Category Name" required />
            </div>

            {{-- Description --}}
            <div class="mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-textarea label="Description" name="description" id="description"
                    placeholder="Category Description" rows="3" />
            </div>

            {{-- Active Checkbox --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 150ms;">
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

            function openSkillCategoryDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Skill Category');
                    $('#drawerButtonText').text('Update Skill Category');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Skill Category');
                    $('#drawerButtonText').text('Save Skill Category');
                }
                openGlobalDrawer('skill-category-drawer', 'skill-category-overlay');
            }

            function resetForm() {
                $('#skillCategoryForm')[0].reset();
                $('#category_id').val('');
                $('#is_active').prop('checked', true);
            }

            function skillCategoryEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching skill category details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();
                let fetchUrl = "{{ route('skill-categories.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let d = res.category;
                        $('#category_id').val(d.id);
                        $('input[name="name"]').val(d.name);
                        $('textarea[name="description"]').val(d.description);
                        $('#is_active').prop('checked', d.is_active == 1);

                        openSkillCategoryDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Failed to fetch data.', 'error');
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }

            function saveForm() {
                if (isSaving) return;

                let id = $('#category_id').val();
                let url = id ? "{{ route('skill-categories.update', ':id') }}".replace(':id', id) :
                    "{{ route('skill-categories.store') }}";

                let formData = $('#skillCategoryForm').serialize();
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

                        if (res.status === 'success' || res.status === true) {
                            Toastify({
                                text: res.message || 'Saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: {
                                    background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                },
                            }).showToast();
                            closeGlobalDrawer('skill-category-drawer', 'skill-category-overlay');
                            $('#skillCategoryTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Skill Category' : 'Save Skill Category');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Skill Category' : 'Save Skill Category');

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

            function skillCategoryDelete(id) {
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
                        let deleteUrl = "{{ route('skill-categories.destroy', ':id') }}".replace(':id', id);

                        $.post(deleteUrl, {
                            _method: 'DELETE',
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
                                $('#skillCategoryTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message || 'Deletion failed.', 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to communicate with server.', 'error');
                        });
                    }
                });
            }

            $('#resetFilters').on('click', function() {
                $('#filter_status').val('');
                $('.dt-filter-skillCategoryTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddSkillCategory', function(e) {
                    e.preventDefault();
                    openSkillCategoryDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>