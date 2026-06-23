<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($employees as $employee)
    @php
        $rule = $employee->attendanceRule;
        $bgColors = ['from-gray-50 to-blue-50', 'from-gray-50 to-green-50', 'from-gray-50 to-purple-50', 'from-gray-50 to-amber-50'];
        $bgColor = $bgColors[$loop->index % 4];
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gradient-to-r {{ $bgColor }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
                    {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $employee->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $employee->employee_code }}</p>
                </div>
            </div>
        </div>
        <div class="p-4">
            <form class="attendance-rule-form" data-employee="{{ $employee->id }}">
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                {{-- Overtime Section --}}
                <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                    <h4 class="text-xs font-bold text-green-700 uppercase mb-2 flex items-center gap-1"><i class="fas fa-clock"></i> Overtime Settings</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" name="enable_overtime" value="1" class="enable-ot w-3.5 h-3.5 text-green-600 rounded" {{ $rule?->enable_overtime !== false ? 'checked' : '' }}>
                            <span class="font-medium text-gray-700">Enable Overtime Pay</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2 ot-fields">
                            <div>
                                <label class="text-[11px] font-medium text-gray-500">Rate per Hour</label>
                                <input type="number" step="0.01" min="0" name="overtime_rate_per_hour" value="{{ $rule?->overtime_rate_per_hour ?? 0 }}" class="w-full text-xs rounded border-gray-300 focus:border-green-500 focus:ring-green-500 p-1.5">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-gray-500">Multiplier (1.5x = time & half)</label>
                                <input type="number" step="0.01" min="1" name="overtime_multiplier" value="{{ $rule?->overtime_multiplier ?? 1.50 }}" class="w-full text-xs rounded border-gray-300 focus:border-green-500 focus:ring-green-500 p-1.5">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Late Section --}}
                <div class="mb-4 p-3 bg-red-50 rounded-lg border border-red-200">
                    <h4 class="text-xs font-bold text-red-700 uppercase mb-2 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> Late Settings</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" name="enable_late_deduction" value="1" class="enable-late w-3.5 h-3.5 text-red-600 rounded" {{ $rule?->enable_late_deduction !== false ? 'checked' : '' }}>
                            <span class="font-medium text-gray-700">Enable Late Deduction</span>
                        </label>
                        <div class="late-fields space-y-2">
                            <div>
                                <label class="text-[11px] font-medium text-gray-500">Deduction Type</label>
                                <select name="late_deduction_type" class="late-deduction-type w-full text-xs rounded border-gray-300 focus:border-red-500 focus:ring-red-500 p-1.5">
                                    <option value="none" {{ $rule?->late_deduction_type == 'none' ? 'selected' : '' }}>No Deduction</option>
                                    <option value="per_minute" {{ $rule?->late_deduction_type == 'per_minute' || !$rule ? 'selected' : '' }}>Per Minute (Rate × Minutes)</option>
                                    <option value="half_day" {{ $rule?->late_deduction_type == 'half_day' ? 'selected' : '' }}>Half Day Salary Deduction</option>
                                    <option value="full_day" {{ $rule?->late_deduction_type == 'full_day' ? 'selected' : '' }}>Full Day Salary Deduction</option>
                                </select>
                            </div>

                            {{-- Per Minute Fields --}}
                            <div class="late-per-minute-fields space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[11px] font-medium text-gray-500">Rate per Minute</label>
                                        <input type="number" step="0.0001" min="0" name="late_deduction_per_minute" value="{{ $rule?->late_deduction_per_minute ?? 0 }}" class="w-full text-xs rounded border-gray-300 focus:border-red-500 focus:ring-red-500 p-1.5">
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-medium text-gray-500">Grace Minutes</label>
                                        <input type="number" min="0" name="late_grace_minutes" value="{{ $rule?->late_grace_minutes ?? 0 }}" class="w-full text-xs rounded border-gray-300 focus:border-red-500 focus:ring-red-500 p-1.5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Half Day & Absent Section --}}
                <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <h4 class="text-xs font-bold text-orange-700 uppercase mb-2 flex items-center gap-1"><i class="fas fa-calendar-minus"></i> Half Day & Absent</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" name="enable_half_day_deduction" value="1" class="w-3.5 h-3.5 text-orange-600 rounded" {{ $rule?->enable_half_day_deduction !== false ? 'checked' : '' }}>
                            <span class="font-medium text-gray-700">Enable Half Day Deduction</span>
                        </label>
                        <div>
                            <label class="text-[11px] font-medium text-gray-500">Half Day Deduction %</label>
                            <input type="number" step="0.01" min="0" max="100" name="half_day_deduction_percent" value="{{ $rule?->half_day_deduction_percent ?? 50 }}" class="w-full text-xs rounded border-gray-300 focus:border-orange-500 focus:ring-orange-500 p-1.5">
                        </div>
                        <label class="flex items-center gap-2 text-xs mt-2">
                            <input type="checkbox" name="enable_absent_deduction" value="1" class="w-3.5 h-3.5 text-orange-600 rounded" {{ $rule?->enable_absent_deduction !== false ? 'checked' : '' }}>
                            <span class="font-medium text-gray-700">Enable Absent Deduction</span>
                        </label>
                        <div>
                            <label class="text-[11px] font-medium text-gray-500">Absent Deduction Days</label>
                            <input type="number" step="0.01" min="0" name="absent_deduction_days" value="{{ $rule?->absent_deduction_days ?? 1 }}" class="w-full text-xs rounded border-gray-300 focus:border-orange-500 focus:ring-orange-500 p-1.5">
                        </div>
                    </div>
                </div>

                <button type="submit" class="save-rule-btn w-full px-3 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-save"></i> <span>Save Rules</span>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 bg-white rounded-xl border border-gray-200">
        <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
        <p class="text-gray-500 text-sm">No active employees found.</p>
    </div>
    @endforelse
</div>