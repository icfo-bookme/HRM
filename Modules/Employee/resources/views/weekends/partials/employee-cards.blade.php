<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($employees as $employee)
    @php
        $weekendDays = $employee->weekend?->weekend_days ?? [];
        $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50">
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
            <form class="weekend-form" data-employee="{{ $employee->id }}">
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                
                <label class="block text-xs font-semibold text-gray-600 mb-2">Weekend Days</label>
                <div class="grid grid-cols-7 gap-1 mb-3">
                    @foreach($dayLabels as $val => $label)
                    <label class="flex flex-col items-center gap-0.5 p-1.5 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-red-50 has-[:checked]:border-red-300 transition">
                        <input type="checkbox" name="weekend_days[]" value="{{ $val }}" class="weekend-day-cb w-3.5 h-3.5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                            {{ in_array($val, $weekendDays) ? 'checked' : '' }}>
                        <span class="text-[10px] font-medium text-gray-600">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                <button type="submit" class="save-weekend-btn w-full px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-save"></i> <span>Save</span>
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