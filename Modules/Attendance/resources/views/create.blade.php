<x-app-layout>
    <div class="p-6 max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-calendar-check text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Give Attendance</h2>
                    <p class="text-sm text-gray-500">Mark your attendance for today</p>
                </div>
            </div>

            <form id="attendanceForm">
                @csrf
                <input type="hidden" name="id" id="attendance_id" value="{{ $todayAttendance->id ?? '' }}">
                <input type="hidden" name="employee_id" id="employee_id" value="{{ $employee->id ?? '' }}">

                {{-- Employee Info Card --}}
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-lg"
                            id="employee_avatar">
                            {{ $employee ? strtoupper(substr($employee->full_name, 0, 1)) : '?' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800" id="employee_name_display">
                                {{ $employee->full_name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500" id="employee_code_display">
                                {{ $employee->employee_code ?? '' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Attendance Date --}}
                <div class="mb-5">
                    <x-form-input type="date" label="Attendance Date" name="attendance_date" id="attendance_date"
                        value="{{ $today }}" required />
                </div>

                {{-- Check In --}}
                <div class="mb-5">
                    <label class="font-semibold text-sm text-slate-700 block mb-1">Check In <span
                            class="text-rose-500 font-bold">*</span></label>
                    <input type="time" id="check_in_at" name="check_in_at"
                        value="{{ $todayAttendance && $todayAttendance->check_in_at
                            ? \Carbon\Carbon::parse($todayAttendance->check_in_at)->format('H:i')
                            : $defaultCheckIn }}"
                        class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                        required />
                </div>

                {{-- Check Out --}}
                <div class="mb-5" id="check_out_container" @style(['display: none' => !($todayAttendance && $todayAttendance->check_in_at)])>
                    <label class="font-semibold text-sm text-slate-700 block mb-1">Check Out</label>
                    <input type="time" id="check_out_at" name="check_out_at"
                        value="{{ $todayAttendance && $todayAttendance->check_out_at ? \Carbon\Carbon::parse($todayAttendance->check_out_at)->format('H:i') : '' }}"
                        class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" />
                </div>

                {{-- Remarks --}}
                <div class="mb-6">
                    <x-form-textarea label="Remarks (Optional)" name="remarks" id="remarks" placeholder="Any notes..."
                        rows="2" />
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center gap-3">
                    <button type="submit" id="btnSubmit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg
                        hover:from-blue-700 hover:to-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        transition-all active:scale-[0.98] shadow-md">
                        <i class="fa-solid fa-check mr-2"></i> Give Attendance
                    </button>
                    <a href="{{ route('attendance.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let isSaving = false;

            $('#attendanceForm').on('submit', function(e) {
                e.preventDefault();
                if (isSaving) return;

                let attendanceId = $('#attendance_id').val();
                let checkIn = $('#check_in_at').val();

                if (!attendanceId && !checkIn) {
                    Swal.fire('Required', 'Please provide a Check In time.', 'warning');
                    return;
                }

                let formData = $(this).serializeArray();
                let attendanceDate = $('input[name="attendance_date"]').val();
                formData.forEach(function(field) {
                    if ((field.name === 'check_in_at' || field.name === 'check_out_at') && field.value) {
                        field.value = attendanceDate + ' ' + field.value + ':00';
                    }
                });

                let data = $.param(formData);
                let url = "{{ route('attendance.store') }}";
                if (attendanceId) {
                    url = "{{ route('attendance.update', ':id') }}".replace(':id', attendanceId);
                    data += '&_method=PUT';
                }

                isSaving = true;
                $('#btnSubmit').prop('disabled', true)
                    .html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(res) {
                        isSaving = false;
                        $('#btnSubmit').prop('disabled', false)
                            .html('<i class="fa-solid fa-check mr-2"></i> Give Attendance');

                        if (res.status === 'success') {
                            Toastify({
                                text: res.message || 'Attendance saved successfully',
                                duration: 3000,
                                gravity: "bottom",
                                position: "right",
                                style: {
                                    background: "linear-gradient(135deg, #16a34a, #4ade80)"
                                },
                            }).showToast();
                            setTimeout(() => {
                                window.location.href = "{{ route('attendance.index') }}";
                            }, 1500);
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                        }
                    },
                    error: function(xhr) {
                        isSaving = false;
                        $('#btnSubmit').prop('disabled', false)
                            .html('<i class="fa-solid fa-check mr-2"></i> Give Attendance');

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
        </script>
    @endpush
</x-app-layout>
