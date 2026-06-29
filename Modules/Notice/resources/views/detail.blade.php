<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Back Button --}}
            <a href="{{ route('notice.list') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 mb-6 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Notice Board
            </a>

            {{-- Notice Detail Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                {{-- Pinned Header --}}
                @if($notice->is_pinned)
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-2 flex items-center gap-2">
                        <i class="fas fa-thumbtack text-white"></i>
                        <span class="text-white text-xs font-semibold uppercase tracking-wider">Pinned Notice</span>
                    </div>
                @endif

                <div class="p-8">
                    {{-- Meta Row --}}
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
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
                        <span class="text-sm text-gray-400">
                            <i class="far fa-calendar-alt mr-1"></i>Published: {{ $notice->publish_date->format('d M Y, h:i A') }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $notice->title }}</h1>

                    {{-- Notice No --}}
                    @if($notice->notice_no)
                        <p class="text-xs text-gray-400 mb-4">Reference: {{ $notice->notice_no }}</p>
                    @endif

                    {{-- Description --}}
                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed mb-8">
                        {!! $notice->description !!}
                    </div>

                    {{-- Attachment --}}
                    @if($notice->attachment_path)
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-paperclip text-indigo-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ basename($notice->attachment_path) }}</p>
                                    <p class="text-xs text-gray-400">Attached file</p>
                                </div>
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($notice->attachment_path) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 bg-white border border-indigo-200 px-4 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Footer Meta --}}
                    <div class="flex items-center justify-between flex-wrap gap-3 pt-4 border-t border-slate-100 text-xs text-gray-400">
                        <span><i class="fas fa-bullseye mr-1"></i>Target: {{ $notice->target_type }}</span>
                        @if($notice->expiry_date)
                            <span><i class="far fa-clock mr-1"></i>Expires: {{ $notice->expiry_date->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>