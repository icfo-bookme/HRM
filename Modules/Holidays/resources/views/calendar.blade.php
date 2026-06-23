<x-app-layout>
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-600"></i>
                    Holiday Calendar
                </h2>
                <div class="flex items-center gap-2">
                    <button id="btnRefreshCalendar" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                    <a href="{{ route('holidays.index') }}" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-list mr-1"></i> List View
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div id="holidayCalendar"></div>
            </div>
        </div>
    </div>

    {{-- Holiday View Modal --}}
    <div id="viewHolidayModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" id="viewModalOverlay"></div>
            <div class="relative inline-block w-full max-w-md bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 text-left overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-green-600"></i>
                        Holiday Details
                    </h3>
                    <button type="button" id="viewModalClose"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-200/50 rounded-lg p-1.5">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-6" id="viewHolidayContent"></div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-end">
                    <button type="button" id="btnViewClose"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Reusable Drawer --}}
    <x-drawer id="holiday-calendar-drawer" overlayId="holiday-calendar-overlay" title="Create Holiday" submitBtnText="Save Holiday" submitOnClick="saveCalendarHoliday()">
        <form id="holidayCalendarForm">
            <input type="hidden" name="selected_dates" id="selected_dates_input">

            <div class="mb-4">
                <label class="font-semibold text-sm text-slate-700 block mb-1">Selected Dates</label>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p id="datesDisplay" class="text-sm text-blue-900 font-medium"></p>
                </div>
            </div>

            <div class="mb-4">
                <x-form-input label="Holiday Name" name="name" placeholder="e.g. Eid-ul-Fitr" required />
            </div>

            <div class="mb-4">
                <x-form-select label="Holiday Type" name="holiday_type" placeholder="Select Type" required>
                    <option value="Public">Public</option>
                    <option value="Government">Government</option>
                    <option value="Company">Company</option>
                    <option value="Optional">Optional</option>
                    <option value="Religious">Religious</option>
                    <option value="Festival">Festival</option>
                </x-form-select>
            </div>

            <div class="mb-4">
                <x-form-select label="Applicable To" name="applicable_to" placeholder="Select Applicable To" required>
                    <option value="All">All</option>
                    <option value="Specific">Specific</option>
                    <option value="Branch">Branch</option>
                    <option value="Department">Department</option>
                </x-form-select>
            </div>

            <div class="mb-4">
                <x-form-textarea label="Description" name="description" placeholder="Enter holiday description" rows="3" />
            </div>

            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_recurring" value="0">
                    <input type="checkbox" id="is_recurring" name="is_recurring" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Is Recurring</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="yearly_recurring" value="0">
                    <input type="checkbox" id="yearly_recurring" name="yearly_recurring" value="1"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Yearly Recurring</span>
                </label>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.15/index.global.min.js"></script>
        <script>
            $(document).ready(function() {
                var csrfToken = '{{ csrf_token() }}';
                var isSaving = false;
                var selectedDates = [];
                var calendarEl = document.getElementById('holidayCalendar');
                var calendar = null;

                function initCalendar() {
                    if (!calendarEl) return;

                    calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek'
                        },
                        height: 'auto',
                        selectable: false,
                        unselectAuto: true,
                        dayMaxEvents: 3,
                        moreLinkContent: function(args) {
                            return '+' + args.num + ' more';
                        },
                        eventClick: function(info) {
                            if (info.event.extendedProps.holiday_id) {
                                showHolidayDetails(info.event);
                            }
                        },
                        dateClick: function(info) {
                            toggleDateSelection(info.dateStr);
                        },
                        events: {
                            url: '{{ route('holidays.calendar.data') }}',
                            failure: function() {
                                console.error('Failed to load holidays');
                            }
                        },
                        eventDidMount: function(info) {
                            if (info.event.extendedProps.description) {
                                info.el.title = info.event.extendedProps.description;
                            }
                        },
                        loading: function(isLoading) {
                            if (isLoading) {
                                $('#holidayCalendar').addClass('opacity-50');
                            } else {
                                $('#holidayCalendar').removeClass('opacity-50');
                                if (selectedDates.length > 0) {
                                    setTimeout(updateSelectedDateStyles, 100);
                                }
                            }
                        }
                    });

                    calendar.render();
                    setTimeout(updateSelectedDateStyles, 200);
                }

                // Toggle date selection
                function toggleDateSelection(dateStr) {
                    // Check if this date already has a holiday event
                    if (calendar) {
                        var events = calendar.getEvents().filter(function(e) {
                            var start = e.startStr.slice(0, 10);
                            var end = e.end ? e.endStr.slice(0, 10) : start;
                            return dateStr >= start && dateStr <= end;
                        });
                        if (events.length > 0) {
                            showHolidayDetails(events[0]);
                            return;
                        }
                    }

                    var idx = selectedDates.indexOf(dateStr);
                    if (idx > -1) {
                        selectedDates.splice(idx, 1);
                    } else {
                        selectedDates.push(dateStr);
                    }
                    selectedDates.sort();
                    updateSelectedDateStyles();
                    updateDatesDisplay();
                    updateCreateButton();
                }

                // Update selected date styles using jQuery
                function updateSelectedDateStyles() {
                    // Remove fc-day-selected from all days
                    $('#holidayCalendar td.fc-daygrid-day').removeClass('fc-day-selected bg-blue-200');

                    // Add fc-day-selected and bg-blue-200 to selected dates
                    $.each(selectedDates, function(i, dateStr) {
                        var dayEl = $('#holidayCalendar').find('[data-date="' + dateStr + '"]');
                        if (dayEl.length) {
                            dayEl.addClass('fc-day-selected bg-blue-200');
                        }
                    });
                }

                // Show/hide the create button
                function updateCreateButton() {
                    if (selectedDates.length > 0) {
                        $('#btnCreateHoliday').removeClass('hidden').addClass('flex');
                    } else {
                        $('#btnCreateHoliday').addClass('hidden').removeClass('flex');
                    }
                }

                // Update dates display in drawer
                function updateDatesDisplay() {
                    if (selectedDates.length === 0) {
                        $('#datesDisplay').text('No dates selected');
                        return;
                    }
                    var formatted = $.map(selectedDates, function(d) {
                        var parts = d.split('-');
                        return new Date(parts[0], parts[1] - 1, parts[2]).toLocaleDateString('en-GB', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                    });
                    $('#datesDisplay').text(formatted.join(', '));
                }

                // Open drawer
                function openCalendarDrawer() {
                    updateDatesDisplay();
                    $('#drawerTitle').text('Create Holiday');
                    $('#drawerButtonText').text('Save Holiday');
                    $('#holidayCalendarForm')[0].reset();
                    $('#is_recurring, #yearly_recurring').prop('checked', false);
                    openGlobalDrawer('holiday-calendar-drawer', 'holiday-calendar-overlay');
                }

                // Show holiday details
                function showHolidayDetails(event) {
                    var ext = event.extendedProps;
                    var startDate = event.start ? event.start.toLocaleDateString('en-GB', {
                        day: 'numeric', month: 'long', year: 'numeric'
                    }) : '';
                    var endDate = event.end ? new Date(event.end.getTime() - 86400000).toLocaleDateString('en-GB', {
                        day: 'numeric', month: 'long', year: 'numeric'
                    }) : '';

                    var typeColors = {
                        'Public': 'bg-blue-100 text-blue-800',
                        'Government': 'bg-red-100 text-red-800',
                        'Company': 'bg-green-100 text-green-800',
                        'Optional': 'bg-yellow-100 text-yellow-800',
                        'Religious': 'bg-purple-100 text-purple-800',
                        'Festival': 'bg-pink-100 text-pink-800',
                    };
                    var typeColor = typeColors[ext.holiday_type] || 'bg-slate-100 text-slate-700';

                    var html = '<div class="space-y-3">' +
                        '<div class="flex items-center gap-3">' +
                            '<div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">' +
                                '<i class="fa-solid fa-calendar-day text-green-600 text-lg"></i>' +
                            '</div>' +
                            '<div>' +
                                '<h4 class="font-semibold text-gray-900 text-lg">' + event.title + '</h4>' +
                                '<span class="' + typeColor + ' text-xs font-medium px-2.5 py-0.5 rounded-full inline-block">' + (ext.holiday_type || 'N/A') + '</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="border-t border-gray-100 pt-3 space-y-2">' +
                            '<div class="flex items-center gap-2 text-sm">' +
                                '<i class="fas fa-calendar text-gray-400 w-4"></i>' +
                                '<span class="text-gray-600">Date:</span>' +
                                '<span class="font-medium text-gray-800">' + startDate + '</span>' +
                            '</div>';
                    if (event.end) {
                        html += '<div class="flex items-center gap-2 text-sm">' +
                                    '<i class="fas fa-calendar-check text-gray-400 w-4"></i>' +
                                    '<span class="text-gray-600">End Date:</span>' +
                                    '<span class="font-medium text-gray-800">' + endDate + '</span>' +
                                '</div>';
                    }
                    html += '<div class="flex items-center gap-2 text-sm">' +
                                '<i class="fas fa-users text-gray-400 w-4"></i>' +
                                '<span class="text-gray-600">Applicable To:</span>' +
                                '<span class="font-medium text-gray-800">' + (ext.applicable_to || 'All') + '</span>' +
                            '</div>';
                    if (ext.is_recurring) {
                        html += '<div class="flex items-center gap-2 text-sm">' +
                                    '<i class="fas fa-sync text-gray-400 w-4"></i>' +
                                    '<span class="text-gray-600">Recurring:</span>' +
                                    '<span class="font-medium text-gray-800">' + (ext.yearly_recurring ? 'Yearly' : 'Yes') + '</span>' +
                                '</div>';
                    }
                    if (ext.description) {
                        html += '<div class="border-t border-gray-100 pt-2">' +
                                    '<p class="text-sm text-gray-500">' + ext.description + '</p>' +
                                '</div>';
                    }
                    html += '</div>' +
                            '<div class="flex gap-2 pt-2">' +
                                '<button onclick="deleteHolidayFromCalendar(' + ext.holiday_id + ')" class="flex-1 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100">' +
                                    '<i class="fas fa-trash mr-1"></i> Delete' +
                                '</button>' +
                            '</div>' +
                        '</div>';

                    $('#viewHolidayContent').html(html);
                    $('#viewHolidayModal').removeClass('hidden');
                    $('body').addClass('overflow-hidden');
                }

                // Save holiday
                window.saveCalendarHoliday = function() {
                    if (isSaving) return;

                    var name = $('#holidayCalendarForm input[name="name"]').val().trim();
                    var holidayType = $('#holidayCalendarForm select[name="holiday_type"]').val();
                    var applicableTo = $('#holidayCalendarForm select[name="applicable_to"]').val();

                    if (!name) { Swal.fire('Validation Error', 'Please enter a holiday name.', 'warning'); return; }
                    if (!holidayType) { Swal.fire('Validation Error', 'Please select a holiday type.', 'warning'); return; }
                    if (!applicableTo) { Swal.fire('Validation Error', 'Please select applicable to.', 'warning'); return; }
                    if (selectedDates.length === 0) { Swal.fire('Validation Error', 'No dates selected.', 'warning'); return; }

                    isSaving = true;
                    $('#drawerButtonText').text('Saving...');
                    $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                    var formData = $('#holidayCalendarForm').serialize();
                    formData += '&selected_dates=' + JSON.stringify(selectedDates);
                    formData += '&_token=' + csrfToken;

                    $.ajax({
                        url: '{{ route('holidays.calendar.store') }}',
                        type: 'POST',
                        data: formData,
                        success: function(res) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text('Save Holiday');
                            if (res.status === true) {
                                Toastify({ text: res.message, duration: 3000, gravity: "bottom", position: "right",
                                    backgroundColor: "linear-gradient(135deg, #0f172a, #1e1b4b)" }).showToast();
                                closeGlobalDrawer('holiday-calendar-drawer', 'holiday-calendar-overlay');
                                selectedDates = [];
                                calendar.refetchEvents();
                                updateSelectedDateStyles();
                                updateCreateButton();
                            } else {
                                Swal.fire('Error', res.message || 'Failed to save holidays.', 'error');
                            }
                        },
                        error: function(xhr) {
                            isSaving = false;
                            $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                            $('#drawerButtonText').text('Save Holiday');
                            Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred', 'error');
                        }
                    });
                };

                // Close view modal
                function closeViewModal() {
                    $('#viewHolidayModal').addClass('hidden');
                    $('body').removeClass('overflow-hidden');
                }

                // Floating button
                var floatingBtn = $(
                    '<button id="btnCreateHoliday" class="fixed bottom-6 right-6 z-40 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg px-5 py-3 items-center gap-2 transition-all hover:scale-105 hidden" style="box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4);">' +
                    '<i class="fas fa-plus"></i>' +
                    '<span class="font-medium">Create Holiday</span>' +
                    '</button>');
                $('body').append(floatingBtn);
                $(document).on('click', '#btnCreateHoliday', function() {
                    if (selectedDates.length > 0) { openCalendarDrawer(); }
                });

                // Event listeners
                $('#btnRefreshCalendar').on('click', function() {
                    if (calendar) { calendar.refetchEvents(); }
                });
                $('#viewModalClose, #btnViewClose, #viewModalOverlay').on('click', closeViewModal);

                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape' && !$('#viewHolidayModal').hasClass('hidden')) closeViewModal();
                });

                // Init calendar
                if (typeof FullCalendar !== 'undefined') {
                    initCalendar();
                } else {
                    var checkFC = setInterval(function() {
                        if (typeof FullCalendar !== 'undefined') {
                            clearInterval(checkFC);
                            initCalendar();
                        }
                    }, 100);
                    setTimeout(function() { clearInterval(checkFC); }, 10000);
                }

                window.closeViewModalGlobal = closeViewModal;
            });

            // Global delete function
            window.deleteHolidayFromCalendar = function(id) {
                var csrfToken = '{{ csrf_token() }}';
                Swal.fire({
                    title: 'Delete Holiday?',
                    text: "This cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563'
                }).then(function(r) {
                    if (r.isConfirmed) {
                        $.post("{{ route('holidays.destroy', ':id') }}".replace(':id', id),
                            { _method: 'DELETE', _token: csrfToken },
                            function(res) {
                                if (res.status) {
                                    Toastify({ text: res.message, duration: 3000, gravity: "bottom", position: "right",
                                        backgroundColor: "red" }).showToast();
                                    if (window.closeViewModalGlobal) window.closeViewModalGlobal();
                                    if (window.calendar) window.calendar.refetchEvents();
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            }).fail(function() { Swal.fire('Error', 'Failed to delete holiday.', 'error'); });
                    }
                });
            };
        </script>
    @endpush

    @push('styles')
        <style>
            .fc { font-family: inherit; }
            .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; }
            .fc .fc-button-primary { background-color: #e2e8f0; border-color: #cbd5e1; color: #475569; font-weight: 500; font-size: 0.875rem; padding: 0.375rem 0.75rem; border-radius: 0.5rem; transition: all 0.15s ease; }
            .fc .fc-button-primary:not(:disabled):hover { background-color: #cbd5e1; border-color: #94a3b8; color: #1e293b; }
            .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #1e3a8a; border-color: #1e3a8a; color: white; }
            .fc .fc-button-primary:disabled { opacity: 0.5; }
            .fc .fc-today-button { text-transform: capitalize; }
            .fc .fc-daygrid-day-number { font-size: 0.875rem; font-weight: 500; color: #475569; padding: 0.5rem 0.5rem 0 0; }
            .fc .fc-daygrid-day.fc-day-today { background-color: #eff6ff; }
            .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { background-color: #1e3a8a; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0.35rem 0.35rem 0 0; font-weight: 600; }
            .fc .fc-daygrid-day.fc-day-selected,
            .fc .fc-daygrid-day.bg-blue-200 { background-color: #bfdbfe !important; border-radius: 0.375rem; }
            .fc .fc-daygrid-day.fc-day-selected .fc-daygrid-day-number,
            .fc .fc-daygrid-day.bg-blue-200 .fc-daygrid-day-number { background-color: #2563eb; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0.35rem 0.35rem 0 0; font-weight: 600; }
            .fc .fc-daygrid-day { cursor: pointer; transition: background-color 0.1s ease; }
            .fc .fc-daygrid-day:hover { background-color: #f8fafc; }
            .fc .fc-daygrid-day.fc-day-selected:hover,
            .fc .fc-daygrid-day.bg-blue-200:hover { background-color: #93c5fd !important; }
            .fc .fc-daygrid-more-link { color: #2563eb; font-weight: 500; font-size: 0.75rem; padding: 0 0.25rem; }
            .fc .fc-event { border-radius: 0.375rem; border: none; padding: 1px 4px; font-size: 0.75rem; font-weight: 500; cursor: pointer; transition: transform 0.1s ease; }
            .fc .fc-event:hover { transform: scale(1.02); filter: brightness(1.1); }
            .fc .fc-event-title { font-weight: 500; padding: 0 2px; }
            .fc .fc-col-header-cell-cushion { font-weight: 600; font-size: 0.75rem; text-transform: uppercase; color: #64748b; padding: 0.625rem 0; }
            .fc .fc-scrollgrid { border-color: #e2e8f0; border-radius: 0.5rem; }
            .fc .fc-scrollgrid-section-header td { border-color: #e2e8f0; }
            .fc .fc-day-other .fc-daygrid-day-number { color: #94a3b8; }
            .fc .fc-scroller-liquid-absolute::-webkit-scrollbar { width: 6px; }
            .fc .fc-scroller-liquid-absolute::-webkit-scrollbar-track { background: #f1f5f9; }
            .fc .fc-scroller-liquid-absolute::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
            .fc .fc-scroller-liquid-absolute::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
    @endpush
</x-app-layout>