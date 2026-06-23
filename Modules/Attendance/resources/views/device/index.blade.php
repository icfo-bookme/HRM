<x-app-layout>

    <div class="p-4">
        <div
            class="flex flex-col md:flex-row md:items-end gap-4 mb-5 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

            {{-- Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Status" id="filter_status" class="dt-filter-attendanceDeviceTable">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-form-select>
            </div>

            {{-- Device Type Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Device Type" id="filter_device_type" class="dt-filter-attendanceDeviceTable">
                    <option value="">All Types</option>
                    <option value="Fingerprint">Fingerprint</option>
                    <option value="Face">Face</option>
                    <option value="Card">Card</option>
                    <option value="Mobile App">Mobile App</option>
                    <option value="Web">Web</option>
                    <option value="Manual">Manual</option>
                </x-form-select>
            </div>

            {{-- Sync Status Filter --}}
            <div class="flex flex-col w-full md:w-1/4">
                <x-form-select label="Sync Status" id="filter_sync_status" class="dt-filter-attendanceDeviceTable">
                    <option value="">All Sync Status</option>
                    <option value="Online">Online</option>
                    <option value="Offline">Offline</option>
                    <option value="Syncing">Syncing</option>
                    <option value="Error">Error</option>
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
        <x-data-table id="attendanceDeviceTable" title="Attendance Devices Management" icon="fa-solid fa-microchip"
            buttonId="btnAddAttendanceDevice" buttonText="Add New Device" :columns="[ 'Device Name', 'Device Code', 'Device Type', 'Serial Number', 'IP Address', 'Sync Status', 'Status', 'Last Sync', 'Created At', 'Action']" :ajaxUrl="route('attendance.devices.dataTable')"
            :dtColumns="[
                ['data' => 'device_name'],
                ['data' => 'device_code'],
                ['data' => 'device_type'],
                ['data' => 'serial_number'],
                ['data' => 'ip_address'],
                ['data' => 'sync_status'],
                ['data' => 'is_active'],
                ['data' => 'last_sync_at'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" :filters="[
                'is_active' => '#filter_status',
                'device_type' => '#filter_device_type',
                'sync_status' => '#filter_sync_status',
            ]" :exportButtons="true" />
    </div>

    {{-- DRAWER COMPONENT --}}
    <x-drawer id="attendance-device-drawer" overlayId="attendance-device-overlay" title="Add New Device"
        submitOnClick="saveForm()">
        <form id="attendanceDeviceForm">
            <input type="hidden" name="id" id="attendance_device_id">

            {{-- Branch Select --}}
            <div class="mb-4 animate-fade" style="animation-delay: 50ms;">
                <x-form-select label="Branch" name="branch_id" id="branch_id" placeholder="Select Branch">
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </x-form-select>
            </div>

            {{-- Device Name & Code --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 100ms;">
                <x-form-input label="Device Name" name="device_name" id="device_name" placeholder="Device Name" required />
                <x-form-input label="Device Code" name="device_code" id="device_code" placeholder="Device Code" required />
            </div>

            {{-- Device Type & Brand --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 150ms;">
                <x-form-select label="Device Type" name="device_type" id="device_type" placeholder="Select Device Type" required>
                    <option value="">-- Select --</option>
                    <option value="Fingerprint">Fingerprint</option>
                    <option value="Face">Face</option>
                    <option value="Card">Card</option>
                    <option value="Mobile App">Mobile App</option>
                    <option value="Web">Web</option>
                    <option value="Manual">Manual</option>
                </x-form-select>
                <x-form-input label="Brand" name="brand" id="brand" placeholder="Brand (Optional)" />
            </div>

            {{-- Model & Serial Number --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 200ms;">
                <x-form-input label="Model" name="model" id="model" placeholder="Model (Optional)" />
                <x-form-input label="Serial Number" name="serial_number" id="serial_number" placeholder="Serial Number" required />
            </div>

            {{-- IP Address & Port --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 250ms;">
                <x-form-input label="IP Address" name="ip_address" id="ip_address" placeholder="192.168.1.1" />
                <x-form-input label="Port" name="port" id="port" placeholder="Port (e.g. 4370)" />
            </div>

            {{-- Communication Type & Firmware Version --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 300ms;">
                <x-form-select label="Communication Type" name="communication_type" id="communication_type" placeholder="Select Communication Type">
                    <option value="">-- Select --</option>
                    <option value="LAN">LAN</option>
                    <option value="WAN">WAN</option>
                    <option value="WiFi">WiFi</option>
                    <option value="Cloud API">Cloud API</option>
                    <option value="USB">USB</option>
                </x-form-select>
                <x-form-input label="Firmware Version" name="firmware_version" id="firmware_version" placeholder="Firmware Version" />
            </div>

            {{-- Timezone & Location --}}
            <div class="grid grid-cols-2 gap-3 mb-4 animate-fade" style="animation-delay: 350ms;">
                <x-form-input label="Timezone" name="timezone" id="timezone" placeholder="Asia/Dhaka" value="Asia/Dhaka" />
                <x-form-input label="Location" name="location" id="location" placeholder="Location (Optional)" />
            </div>

            {{-- Sync Status --}}
            <div class="mb-4 animate-fade" style="animation-delay: 400ms;">
                <x-form-select label="Sync Status" name="sync_status" id="sync_status" placeholder="Select Sync Status">
                    <option value="">-- Select --</option>
                    <option value="Online">Online</option>
                    <option value="Offline">Offline</option>
                    <option value="Syncing">Syncing</option>
                    <option value="Error">Error</option>
                </x-form-select>
            </div>

            {{-- Notes --}}
            <div class="mb-4 animate-fade" style="animation-delay: 450ms;">
                <x-form-textarea label="Notes" name="notes" id="notes"
                    placeholder="Device Notes (Optional)" rows="3" />
            </div>

            {{-- Active Checkbox --}}
            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 animate-fade"
                style="animation-delay: 500ms;">
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

            function openAttendanceDeviceDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Attendance Device');
                    $('#drawerButtonText').text('Update Device');
                } else {
                    resetForm();
                    $('#drawerTitle').text('Add New Attendance Device');
                    $('#drawerButtonText').text('Save Device');
                }
                openGlobalDrawer('attendance-device-drawer', 'attendance-device-overlay');
            }

            function resetForm() {
                $('#attendanceDeviceForm')[0].reset();
                $('#attendance_device_id').val('');
                $('#is_active').prop('checked', true);
                $('#timezone').val('Asia/Dhaka');
            }

            function attendanceDeviceEdit(id) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching attendance device details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                resetForm();
                let fetchUrl = "{{ route('attendance.devices.show', ':id') }}".replace(':id', id);

                $.get(fetchUrl, function(res) {
                    Swal.close();
                    if (res.status) {
                        let d = res.device;
                        $('#attendance_device_id').val(d.id);
                        $('select[name="branch_id"]').val(d.branch_id || '');
                        $('input[name="device_name"]').val(d.device_name);
                        $('input[name="device_code"]').val(d.device_code);
                        $('select[name="device_type"]').val(d.device_type || '');
                        $('input[name="brand"]').val(d.brand);
                        $('input[name="model"]').val(d.model);
                        $('input[name="serial_number"]').val(d.serial_number);
                        $('input[name="ip_address"]').val(d.ip_address);
                        $('input[name="port"]').val(d.port);
                        $('select[name="communication_type"]').val(d.communication_type || '');
                        $('input[name="firmware_version"]').val(d.firmware_version);
                        $('input[name="timezone"]').val(d.timezone);
                        $('input[name="location"]').val(d.location);
                        $('select[name="sync_status"]').val(d.sync_status || '');
                        $('textarea[name="notes"]').val(d.notes);

                        $('#is_active').prop('checked', d.is_active == 1);

                        openAttendanceDeviceDrawer('edit');
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

                let id = $('#attendance_device_id').val();
                let url = id ? "{{ route('attendance.devices.update', ':id') }}".replace(':id', id) :
                    "{{ route('attendance.devices.store') }}";

                let formData = $('#attendanceDeviceForm').serialize();
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
                            closeGlobalDrawer('attendance-device-drawer', 'attendance-device-overlay');
                            $('#attendanceDeviceTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                            $('#drawerButtonText').text(id ? 'Update Device' : 'Save Device');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(id ? 'Update Device' : 'Save Device');

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

            function attendanceDeviceDelete(id) {
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
                        let deleteUrl = "{{ route('attendance.devices.destroy', ':id') }}".replace(':id', id);

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
                                $('#attendanceDeviceTable').DataTable().ajax.reload(null, false);
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
                $('#filter_device_type').val('');
                $('#filter_sync_status').val('');

                $('.dt-filter-attendanceDeviceTable').trigger('change');
            });

            $(document).ready(function() {
                $(document).on('click', '#btnAddAttendanceDevice', function(e) {
                    e.preventDefault();
                    openAttendanceDeviceDrawer('add');
                });
            });
        </script>
    @endpush
</x-app-layout>