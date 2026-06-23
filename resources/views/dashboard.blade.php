<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- LEFT SIDE: Latest Notice Detail (col-span-3) --}}
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-bullhorn text-indigo-600 text-xl"></i>
                                    <h3 class="text-lg font-semibold text-gray-800">Latest Notice</h3>
                                </div>
                                <a href="{{ route('notice.list') }}" class="text-xs text-indigo-600 hover:text-indigo-800 underline">View All Notices</a>
                            </div>

                            @php
                                $latestNotice = $latestNotices->first();
                            @endphp

                            @if(!$latestNotice)
                                <div class="text-center py-8">
                                    <i class="fa-solid fa-bullhorn text-4xl text-slate-300 mb-3"></i>
                                    <p class="text-gray-500 text-sm">No notices available.</p>
                                </div>
                            @else
                                @php
                                    $typeColors = [
                                        'General' => 'bg-slate-100 text-slate-700',
                                        'HR' => 'bg-blue-100 text-blue-700',
                                        'Holiday' => 'bg-green-100 text-green-700',
                                        'Attendance' => 'bg-yellow-100 text-yellow-700',
                                        'Payroll' => 'bg-purple-100 text-purple-700',
                                        'Policy' => 'bg-indigo-100 text-indigo-700',
                                        'Training' => 'bg-pink-100 text-pink-700',
                                        'Event' => 'bg-orange-100 text-orange-700',
                                        'Emergency' => 'bg-red-100 text-red-700',
                                    ];
                                    $priorityColors = [
                                        'Low' => 'bg-green-50 text-green-700 border-green-200',
                                        'Medium' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'High' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'Urgent' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                @endphp
                                <div class="border border-slate-200 rounded-xl overflow-hidden {{ $latestNotice->is_pinned ? 'ring-2 ring-red-300 ring-offset-1' : '' }}">
                                    @if($latestNotice->is_pinned)
                                        <div class="bg-gradient-to-r from-red-500 to-red-600 px-4 py-1.5 flex items-center gap-1.5">
                                            <i class="fas fa-thumbtack text-white text-xs"></i>
                                            <span class="text-white text-[11px] font-semibold uppercase tracking-wider">Pinned Notice</span>
                                        </div>
                                    @endif
                                    <div class="p-6">
                                        {{-- Badges --}}
                                        <div class="flex items-center gap-2 flex-wrap mb-3">
                                            <span class="{{ $typeColors[$latestNotice->notice_type] ?? 'bg-slate-100 text-slate-700' }} text-xs font-medium px-2.5 py-1 rounded-full">{{ $latestNotice->notice_type }}</span>
                                            <span class="{{ $priorityColors[$latestNotice->priority] ?? 'bg-slate-50 text-slate-700' }} text-xs font-medium px-2.5 py-1 rounded-full border">{{ $latestNotice->priority }}</span>
                                            @if($latestNotice->is_popup)
                                                <span class="bg-amber-50 text-amber-700 border border-amber-200 text-xs font-medium px-2.5 py-1 rounded-full"><i class="fas fa-window-restore mr-1"></i>Popup</span>
                                            @endif
                                            <span class="text-xs text-gray-400 ml-auto"><i class="far fa-calendar-alt mr-1"></i>{{ $latestNotice->publish_date->format('d M Y, h:i A') }}</span>
                                        </div>

                                        {{-- Title --}}
                                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $latestNotice->title }}</h3>

                                        {{-- Description --}}
                                        <div class="text-sm text-gray-600 leading-relaxed mb-4">
                                            {!! nl2br(e($latestNotice->description)) !!}
                                        </div>

                                        {{-- Attachment --}}
                                        @if($latestNotice->attachment_path)
                                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 mb-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-paperclip text-indigo-600 text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-700 truncate">{{ basename($latestNotice->attachment_path) }}</p>
                                                    </div>
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($latestNotice->attachment_path) }}" target="_blank"
                                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-white border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Footer --}}
                                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                            <span class="text-xs text-gray-400"><i class="fas fa-bullseye mr-1"></i>Target: {{ $latestNotice->target_type }}</span>
                                            <a href="{{ route('notice.detail', $latestNotice->id) }}"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                                Read More <i class="fas fa-arrow-right text-[10px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT SIDE: Upcoming Holidays & Birthdays (col-span-1) --}}
                <div class="lg:col-span-1 space-y-4">
                    {{-- Upcoming Holidays --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-calendar-day text-indigo-600 text-lg"></i>
                                <h3 class="text-base font-semibold text-gray-800">Upcoming Holidays</h3>
                            </div>

                            @if($upcomingHolidays->isEmpty())
                                <p class="text-gray-500 text-xs">No upcoming holidays found.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($upcomingHolidays as $holiday)
                                        <div class="border border-slate-100 rounded-lg p-3 hover:bg-slate-50 transition-colors">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="text-center leading-tight flex-shrink-0">
                                                    <div class="text-xs font-bold text-indigo-600">{{ $holiday->holiday_date->format('d') }}</div>
                                                    <div class="text-[10px] text-gray-400 uppercase">{{ $holiday->holiday_date->format('M') }}</div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold text-gray-700 truncate">{{ $holiday->name }}</p>
                                                    @php
                                                        $hc = [
                                                            'Public' => 'bg-blue-100 text-blue-700',
                                                            'Government' => 'bg-red-100 text-red-700',
                                                            'Company' => 'bg-green-100 text-green-700',
                                                            'Optional' => 'bg-yellow-100 text-yellow-700',
                                                            'Religious' => 'bg-purple-100 text-purple-700',
                                                            'Festival' => 'bg-pink-100 text-pink-700',
                                                        ][$holiday->holiday_type] ?? 'bg-slate-100 text-slate-700';
                                                    @endphp
                                                    <span class="{{ $hc }} text-[10px] font-medium px-1.5 py-0.5 rounded-full">{{ $holiday->holiday_type }}</span>
                                                    @if($holiday->total_days > 1)
                                                        <span class="text-[10px] text-gray-400 ml-1">{{ $holiday->total_days }} days</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <a href="{{ route('holidays.index') }}" class="block text-center text-xs text-indigo-600 hover:text-indigo-800 underline mt-3">View All</a>
                            @endif
                        </div>
                    </div>

                    {{-- Upcoming Birthdays --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-cake-candles text-pink-500 text-lg"></i>
                                <h3 class="text-base font-semibold text-gray-800">Upcoming Birthdays 🎂</h3>
                            </div>

                            @if($upcomingBirthdays->isEmpty())
                                <p class="text-gray-500 text-xs">No upcoming birthdays.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($upcomingBirthdays as $item)
                                        <div class="border border-slate-100 rounded-lg p-3 hover:bg-pink-50 transition-colors">
                                            <div class="flex items-center gap-2">
                                                {{-- Avatar or icon --}}
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
                                                    @if($item->profile_photo)
                                                        <img src="{{ asset('storage/' . $item->profile_photo) }}" alt="" class="w-full h-full rounded-full object-cover">
                                                    @else
                                                        {{ substr($item->employee_name, 0, 2) }}
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold text-gray-700 truncate">{{ $item->employee_name }}</p>
                                                    <div class="flex items-center gap-1 mt-0.5">
                                                        <span class="text-[10px] text-pink-500 font-medium">
                                                            @if($item->days_until == 0)
                                                                🎉 Today!
                                                            @elseif($item->days_until == 1)
                                                                Tomorrow
                                                            @else
                                                                In {{ $item->days_until }} days
                                                            @endif
                                                        </span>
                                                        <span class="text-[10px] text-gray-400">•</span>
                                                        <span class="text-[10px] text-gray-400">{{ $item->date_of_birth->format('M d') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>