<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-bullhorn text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Notice Board</h2>
                        <p class="text-xs text-gray-500">Stay updated with the latest announcements</p>
                    </div>
                </div>
            </div>

            {{-- Notices Grid --}}
            @if($notices->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                    <i class="fa-solid fa-bullhorn text-4xl text-slate-300 mb-3"></i>
                    <p class="text-gray-500">No notices available at this time.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notices as $notice)
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all {{ $notice->is_pinned ? 'ring-2 ring-red-300 ring-offset-1' : '' }}">
                            {{-- Pinned Badge --}}
                            @if($notice->is_pinned)
                                <div class="bg-gradient-to-r from-red-500 to-red-600 px-4 py-1 flex items-center gap-1.5">
                                    <i class="fas fa-thumbtack text-white text-xs"></i>
                                    <span class="text-white text-[11px] font-semibold uppercase tracking-wider">Pinned Notice</span>
                                </div>
                            @endif

                            <div class="p-6">
                                {{-- Top Row: Type + Priority Badges & Date --}}
                                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                                    <div class="flex items-center gap-2 flex-wrap">
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
                                        <span class="{{ $typeColors[$notice->notice_type] ?? 'bg-slate-100 text-slate-700' }} text-xs font-medium px-2.5 py-1 rounded-full">{{ $notice->notice_type }}</span>
                                        <span class="{{ $priorityColors[$notice->priority] ?? 'bg-slate-50 text-slate-700' }} text-xs font-medium px-2.5 py-1 rounded-full border">{{ $notice->priority }} Priority</span>
                                        @if($notice->is_popup)
                                            <span class="bg-amber-50 text-amber-700 border border-amber-200 text-xs font-medium px-2.5 py-1 rounded-full"><i class="fas fa-window-restore mr-1"></i>Popup</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        <i class="far fa-calendar-alt mr-1"></i>{{ $notice->publish_date->format('d M Y, h:i A') }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $notice->title }}</h3>

                                {{-- Description --}}
                                <div class="text-sm text-gray-600 leading-relaxed mb-4 prose prose-sm max-w-none">
                                    {!! nl2br(e($notice->description)) !!}
                                </div>

                                {{-- Footer: Attachment + Detail Link --}}
                                <div class="flex items-center justify-between flex-wrap gap-3 pt-3 border-t border-slate-100">
                                    <div class="flex items-center gap-3">
                                        @if($notice->attachment_path)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($notice->attachment_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                                <i class="fas fa-paperclip"></i>
                                                {{ basename($notice->attachment_path) }}
                                                <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        @endif
                                        @if($notice->target_type !== 'All')
                                            <span class="text-xs text-gray-400"><i class="fas fa-users mr-1"></i>{{ $notice->target_type }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('notice.detail', $notice->id) }}"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                        Read More <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $notices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>