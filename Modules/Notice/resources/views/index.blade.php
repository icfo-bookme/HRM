<x-app-layout>
    <div class="p-4">
        {{-- FILTERS --}}
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            {{-- Notice Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_notice_type" label="Notice Type" tableId="noticeTable" :options="$noticeTypes" />
            </div>

            {{-- Priority Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_priority" label="Priority" tableId="noticeTable" :options="$priorities" />
            </div>

            {{-- Pinned Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_pinned" label="Pinned Status" tableId="noticeTable" :options="$pinnedStatus" />
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-table-filter id="filter_status" label="Status" tableId="noticeTable" :options="$statuses" />
            </div>

            {{-- Reset Button --}}
            <div class="w-full md:w-auto flex items-end">
                <button id="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800
                   rounded-lg transition active:scale-95">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>

        {{-- REUSABLE DATA-TABLE COMPONENT --}}
        <x-data-table id="noticeTable" title="Notice Board Management" icon="fa-solid fa-bullhorn" buttonId="btnAddNotice"
            buttonText="Add New Notice" :columns="[
                'Title',
                'Notice Type',
                'Priority',
                'Publish Date',
                'Expiry Date',
                'Pinned',
                'Status',
                'Action',
            ]" :ajaxUrl="route('notice.dataTable')" :dtColumns="[
                ['data' => 'title'],
                ['data' => 'notice_type'],
                ['data' => 'priority'],
                ['data' => 'publish_date'],
                ['data' => 'expiry_date'],
                ['data' => 'is_pinned'],
                ['data' => 'is_active'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]"
            :filters="[
                'notice_type' => '#filter_notice_type',
                'priority' => '#filter_priority',
                'is_pinned' => '#filter_pinned',
                'is_active' => '#filter_status',
            ]" />
    </div>

    @push('scripts')
        <script>

            $(document).ready(function() {
                // Open add new employee - redirect to step 1 of the creation wizard
                $(document).on('click', '#btnAddNotice', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('notice.create') }}";
                });
            });
            const csrfToken = '{{ csrf_token() }}';

            function noticeDelete(id) {
                Swal.fire({
                    title: 'Delete?',
                    text: "This cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then((r) => {
                    if (r.isConfirmed) {
                        let deleteUrl = "{{ route('notice.destroy', ':id') }}".replace(':id', id);
                        $.post(deleteUrl, {
                            _method: 'DELETE',
                            _token: csrfToken
                        }, function(res) {
                            if (res.status) {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "bottom",
                                    position: "right",
                                    backgroundColor: "red",
                                }).showToast();
                                $('#noticeTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }).fail(function() {
                            Swal.fire('Error', 'Failed to delete notice.', 'error');
                        });
                    }
                });
            }

            $(document).ready(function() {
                // Reset filters
                $('#resetFilters').on('click', function() {
                    $('#filter_notice_type, #filter_priority, #filter_pinned, #filter_status').val('').trigger('change');
                });
            });
        </script>
    @endpush
</x-app-layout>
