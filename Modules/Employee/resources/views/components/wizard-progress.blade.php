@props(['current' => 1])

@php
    $steps = [
        ['label' => 'Core Info', 'description' => 'Employee basics', 'url' => '/employees/create/step-1'],
        ['label' => 'Personal Info', 'description' => 'Sensitive details', 'url' => '/employees/create/step-2'],
        ['label' => 'Address Info', 'description' => 'Contact & location', 'url' => '/employees/create/step-3'],
        ['label' => 'Banking Info', 'description' => 'Payment setup', 'url' => '/employees/create/step-4'],
        ['label' => 'Documents', 'description' => 'Official files', 'url' => '/employees/create/step-5'],
        ['label' => 'Education', 'description' => 'Qualifications', 'url' => '/employees/create/step-6'],
        ['label' => 'Experience', 'description' => 'Work history', 'url' => '/employees/create/step-7'],
        ['label' => 'Job History', 'description' => 'Career changes', 'url' => '/employees/create/step-8'],
        ['label' => 'Languages', 'description' => 'Language skills', 'url' => '/employees/create/step-9'],
        ['label' => 'Skills', 'description' => 'Competencies', 'url' => '/employees/create/step-10'],
        ['label' => 'Dependents', 'description' => 'Family & nominees', 'url' => '/employees/create/step-11'],
    ];
    $percentage = round(($current / count($steps)) * 100);
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm border border-slate-200 rounded-b-3xl p-6 mb-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Employee Onboarding</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Complete new employee registration</h2>
                <p class="mt-1 text-sm text-slate-500">Capture employee details step by step for a complete, compliant
                    profile.</p>
            </div>
            <div class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">
                Step {{ $current }} of {{ count($steps) }}
            </div>
        </div>

        <div class="mt-6">
            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-bg-blue-900 to-indigo-600 transition-all duration-500"
                    style="width: {{ $percentage }}%"></div>
            </div>
            <div class="mt-3 text-sm font-medium text-slate-700">Progress: {{ $percentage }}%</div>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
            @foreach ($steps as $index => $step)
                @php $stepNumber = $index + 1; @endphp
                <a href="{{ $step['url'] }}">
                    <div
                        class="rounded-2xl border px-4 py-4 transition-colors duration-200 {{ $stepNumber === $current ? 'border-bg-blue-900 bg-sky-50' : ($stepNumber < $current ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-white') }}">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold {{ $stepNumber <= $current ? 'bg-blue-900 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $stepNumber }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">{{ $step['label'] }}</div>
                                {{-- <p class="text-xs text-slate-500">{{ $step['description'] }}</p> --}}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
