<x-app-layout>
    <div class="p-4">
        {{-- HEADER --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">KPI Task Details</h1>
                    <p class="text-sm text-gray-500 mt-1">Task ID: #{{ $task->id }}</p>
                </div>
                <div class="flex gap-2">
                    @if(in_array($task->status, ['Pending', 'In Progress']))
                        <a href="{{ route('kpi.tasks.edit', $task->id) }}"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            <i class="fas fa-edit mr-1"></i> Edit Task
                        </a>
                    @endif
                    <a href="{{ route('kpi.tasks.index') }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            {{-- TASK INFO --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Employee Info --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Employee Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                    {{ $task->employee?->personalInfo?->full_name ? strtoupper(substr($task->employee->personalInfo->full_name, 0, 2)) : 'NA' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $task->employee?->personalInfo?->full_name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $task->employee?->employee_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                {{ $task->employee?->department?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- Task Details --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Task Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Title:</span>
                                <span class="text-sm font-medium text-gray-800">{{ $task->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Priority:</span>
                                <span class="text-sm font-medium">
                                    @php
                                        $priorityColors = [
                                            'Low' => 'bg-gray-100 text-gray-600',
                                            'Medium' => 'bg-blue-100 text-blue-700',
                                            'High' => 'bg-orange-100 text-orange-700',
                                            'Critical' => 'bg-red-100 text-red-700',
                                        ];
                                        $priorityColor = $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $priorityColor }}">
                                        {{ $task->priority }}
                                    </span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium">
                                    @php
                                        $statusColors = [
                                            'Pending' => 'bg-yellow-100 text-yellow-700',
                                            'In Progress' => 'bg-blue-100 text-blue-700',
                                            'Completed' => 'bg-green-100 text-green-700',
                                            'Cancelled' => 'bg-gray-100 text-gray-500',
                                            'Overdue' => 'bg-red-100 text-red-700',
                                        ];
                                        $statusColor = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                        {{ $task->status }}
                                    </span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Assigned Date:</span>
                                <span class="text-sm font-medium text-gray-800">{{ $task->assigned_date?->format('d M Y') ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Deadline:</span>
                                <span class="text-sm font-medium text-gray-800">{{ $task->deadline?->format('d M Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($task->description)
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Description</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $task->description }}</p>
                        </div>
                    </div>
                @endif

                {{-- Scores --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Score Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Target Score:</span>
                                <span class="text-sm font-bold text-gray-800">{{ number_format($task->target_score, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Obtained Score:</span>
                                <span class="text-sm font-bold {{ $task->obtained_score ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $task->obtained_score ? number_format($task->obtained_score, 2) : '-' }}
                                </span>
                            </div>
                            @if($task->obtained_score)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Achievement:</span>
                                    <span class="text-sm font-bold {{ ($task->obtained_score / $task->target_score) >= 1 ? 'text-green-600' : 'text-orange-600' }}">
                                        {{ number_format(($task->obtained_score / $task->target_score) * 100, 1) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Assignment Info</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Assigned By:</span>
                                <span class="text-sm font-medium text-gray-800">{{ $task->assignedBy?->personalInfo?->full_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Completed At:</span>
                                <span class="text-sm font-medium text-gray-800">{{ $task->completed_at?->format('d M Y H:i') ?? '-' }}</span>
                            </div>
                            @if($task->completion_note)
                                <div>
                                    <span class="text-sm text-gray-600">Completion Note:</span>
                                    <p class="text-sm text-gray-800 mt-1">{{ $task->completion_note }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>