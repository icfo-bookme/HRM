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
                    <div class="bg-gradient-to-r from-blue-500 to-blue-800 px-6 py-2 flex items-center gap-2">
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

            {{-- ==================== SEEN BY (Facebook Style - Auto-tracked on Login/Visit) ==================== --}}
            @php
                $totalViews = $viewers->count();
                $maxShow = 3;
                $shownViewers = $viewers->take($maxShow);
                $remainingViewCount = $totalViews - $maxShow;
            @endphp

            @if($totalViews > 0)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            {{-- Stacked Avatars --}}
                            <div class="flex items-center -space-x-2 flex-shrink-0">
                                @foreach($shownViewers as $view)
                                    @php
                                        $emp = $view->employee;
                                        $viewPhoto = $emp?->profile_photo 
                                            ? asset('storage/' . $emp->profile_photo) 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($emp?->full_name ?? 'E') . '&background=6366f1&color=fff&size=40';
                                    @endphp
                                    <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden shadow-sm">
                                        <img src="{{ $viewPhoto }}" alt="{{ $emp?->full_name ?? 'Employee' }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                                @if($remainingViewCount > 0)
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-indigo-100 flex items-center justify-center shadow-sm">
                                        <span class="text-[10px] font-bold text-indigo-600">+{{ $remainingViewCount }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Seen By Text --}}
                            <div class="text-sm text-gray-600">
                                <span class="font-medium text-gray-800">Seen by </span>
                                @foreach($shownViewers as $i => $view)
                                    @php $emp = $view->employee; @endphp
                                    <span class="font-medium text-gray-800">{{ $emp?->full_name ?? 'Unknown' }}</span>@if($i < $shownViewers->count() - 1), @endif
                                @endforeach
                                @if($remainingViewCount > 0)
                                    <span class="text-gray-500"> and {{ $remainingViewCount }} {{ Str::plural('other', $remainingViewCount) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==================== ACKNOWLEDGMENT SECTION (Facebook Comment Style) ==================== --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Acknowledgments
                        <span class="text-sm font-normal text-gray-400">({{ $notice->acknowledgements->count() }})</span>
                    </h3>

                    {{-- Acknowledgment Form --}}
                    <form action="{{ route('notice.acknowledge', $notice->id) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex items-start gap-3">
                            {{-- Current User Avatar --}}
                            <div class="flex-shrink-0">
                                @php
                                    $user = Auth::user();
                                    $employee = $user->employee;
                                    $photo = $employee?->profile_photo 
                                        ? asset('storage/' . $employee->profile_photo) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($employee?->full_name ?? 'User') . '&background=6366f1&color=fff';
                                @endphp
                                <img src="{{ $photo }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                            </div>
                            <div class="flex-1">
                                <div class="relative">
                                    <textarea name="comment" rows="2" 
                                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all @error('comment') border-red-300 @enderror"
                                        placeholder="{{ $myAcknowledgement ? 'Update your acknowledgment...' : 'Write your acknowledgment... (optional)' }}">{{ $myAcknowledgement?->comment }}</textarea>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-400">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        @if($myAcknowledgement)
                                            You have already acknowledged this notice. You can update your comment.
                                        @else
                                            Acknowledge that you have read and understood this notice.
                                        @endif
                                    </p>
                                    <button type="submit" 
                                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                        <i class="fas fa-check"></i>
                                        {{ $myAcknowledgement ? 'Update' : 'Acknowledge' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Divider --}}
                    <hr class="border-slate-200 my-4">

                    {{-- Acknowledgments List (Facebook Comment Style) --}}
                    <div class="space-y-4">
                        @forelse($notice->acknowledgements as $ack)
                            <div class="flex items-start gap-3">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0">
                                    @php
                                        $emp = $ack->employee;
                                        $ackPhoto = $emp?->profile_photo 
                                            ? asset('storage/' . $emp->profile_photo) 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($emp?->full_name ?? 'Employee') . '&background=6366f1&color=fff';
                                    @endphp
                                    <img src="{{ $ackPhoto }}" alt="{{ $emp?->full_name ?? 'Employee' }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                </div>
                                {{-- Comment Bubble --}}
                                <div class="flex-1 min-w-0">
                                    <div class="bg-slate-50 rounded-2xl rounded-tl-sm px-4 py-2.5 border border-slate-100">
                                        <p class="text-sm font-semibold text-gray-800">{{ $emp?->full_name ?? 'Unknown Employee' }}</p>
                                        @if($ack->comment)
                                            <p class="text-sm text-gray-600 mt-0.5">{{ $ack->comment }}</p>
                                        @else
                                            <p class="text-sm text-gray-400 italic mt-0.5">Acknowledged without comment</p>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 ml-1">
                                        <i class="far fa-clock mr-1"></i>{{ $ack->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            {{-- Empty State --}}
                            <div class="text-center py-8">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-comment-dots text-2xl text-slate-300"></i>
                                </div>
                                <p class="text-sm text-gray-500">No acknowledgments yet.</p>
                                <p class="text-xs text-gray-400 mt-1">Be the first to acknowledge this notice.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>