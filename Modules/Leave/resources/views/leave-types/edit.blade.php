<x-app-layout>
    <div class="p-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-slate-800">Edit Leave Type</h2>
            </div>
            <form action="{{ route('leave-types.update', $leaveType->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <x-form-input label="Name" name="name" id="name" placeholder="e.g. Annual Leave" :value="old('name', $leaveType->name)" :required="true" />
                    <x-form-input label="Days Per Year" name="days_per_year" id="days_per_year" type="number" step="0.1" placeholder="e.g. 30" :value="old('days_per_year', $leaveType->days_per_year)" />
                    <x-form-input label="Max Consecutive Days" name="max_consecutive_days" id="max_consecutive_days" type="number" placeholder="e.g. 15" :value="old('max_consecutive_days', $leaveType->max_consecutive_days)" />
                    <x-form-input label="Max Carry Days" name="max_carry_days" id="max_carry_days" type="number" step="0.1" placeholder="e.g. 10" :value="old('max_carry_days', $leaveType->max_carry_days)" />
                    <x-form-input label="Min Days Notice" name="min_days_notice" id="min_days_notice" type="number" placeholder="e.g. 1" :value="old('min_days_notice', $leaveType->min_days_notice)" />
                    <x-form-input label="Color Code" name="color_code" id="color_code" type="text" placeholder="e.g. #FF5733" :value="old('color_code', $leaveType->color_code)" />
                    <x-form-select label="Applicable Gender" name="applicable_gender" id="applicable_gender">
                        <option value="All" {{ old('applicable_gender', $leaveType->applicable_gender) == 'All' ? 'selected' : '' }}>All</option>
                        <option value="Male" {{ old('applicable_gender', $leaveType->applicable_gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('applicable_gender', $leaveType->applicable_gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    </x-form-select>

                    <div>
                        <label class="font-semibold text-sm text-slate-700 block mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full border border-slate-300 rounded-md p-2 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400">{{ old('description', $leaveType->description) }}</textarea>
                    </div>

                    <div class="md:col-span-2 lg:col-span-3 space-y-3">
                        <label class="font-semibold text-sm text-slate-700 block mb-1">Settings</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" name="is_paid" value="1" {{ old('is_paid', $leaveType->is_paid) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Paid Leave</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" name="is_half_day_allowed" value="1" {{ old('is_half_day_allowed', $leaveType->is_half_day_allowed) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Half Day Allowed</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" name="carry_forward" value="1" {{ old('carry_forward', $leaveType->carry_forward) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Carry Forward</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" name="requires_document" value="1" {{ old('requires_document', $leaveType->requires_document) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Requires Document</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-md cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $leaveType->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition active:scale-95">
                        Update Leave Type
                    </button>
                    <a href="{{ route('leave-types.index') }}"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition active:scale-95">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>