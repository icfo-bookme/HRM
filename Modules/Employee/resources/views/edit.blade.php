<x-app-layout>
    @php
        $employee = $employee ?? null;
        $activeTab = request('tab', 'basic');
        $tabs = [
            'basic'       => ['label' => 'Core Info', 'icon' => 'fa-building', 'desc' => 'Employee basics'],
            'personal'    => ['label' => 'Personal Info', 'icon' => 'fa-user', 'desc' => 'Personal & sensitive details'],
            'addresses'   => ['label' => 'Address Info', 'icon' => 'fa-location-dot', 'desc' => 'Contact & location'],
            'banking'     => ['label' => 'Banking Info', 'icon' => 'fa-building-columns', 'desc' => 'Payment setup'],
            'documents'   => ['label' => 'Documents', 'icon' => 'fa-folder-open', 'desc' => 'Official files'],
            'education'   => ['label' => 'Education', 'icon' => 'fa-graduation-cap', 'desc' => 'Qualifications'],
            'experience'  => ['label' => 'Experience', 'icon' => 'fa-briefcase', 'desc' => 'Work history'],
            'job-history' => ['label' => 'Job History', 'icon' => 'fa-timeline', 'desc' => 'Career changes'],
            'languages'   => ['label' => 'Languages', 'icon' => 'fa-language', 'desc' => 'Language skills'],
            'skills'      => ['label' => 'Skills', 'icon' => 'fa-star', 'desc' => 'Competencies'],
            'dependents'  => ['label' => 'Dependents', 'icon' => 'fa-people-group', 'desc' => 'Dependents & nominees'],
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-user-pen text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Edit Employee</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $employee->employee_code ?? '' }} — {{ $employee->full_name ?? 'Loading...' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tab Navigation using Anchor Tags --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
            <div class="flex border-b border-slate-200 overflow-x-auto">
                @foreach ($tabs as $key => $tab)
                    <a href="{{ route('employee.edit', ['id' => $employee->id, 'tab' => $key]) }}"
                       class="tab-link px-4 py-4 text-sm font-semibold whitespace-nowrap transition-colors border-b-2 flex items-center gap-2
                       {{ $activeTab === $key ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid {{ $tab['icon'] }}"></i>
                        <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tab Content Sections --}}
        @include('employee::edit-tabs.basic', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.personal', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.addresses', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.banking', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.documents', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.education', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.experience', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.job-history', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.languages', ['employee' => $employee, 'activeTab' => $activeTab])
        @include('employee::edit-tabs.skills', ['employee' => $employee, 'activeTab' => $activeTab, 'skillCategories' => $skillCategories])
        @include('employee::edit-tabs.dependents', ['employee' => $employee, 'activeTab' => $activeTab])
    </div>

    @push('scripts')
    <script>
        // Generic form submission handler for all section forms
        document.querySelectorAll('.section-save-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = this.getAttribute('action');
                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData,
                })
                .then(r => r.json())
                .then(res => {
                    Swal.close();
                    if (res.status === 'success') {
                        Toastify({
                            text: res.message,
                            duration: 3000,
                            gravity: "bottom",
                            position: "right",
                            style: { background: "linear-gradient(135deg, #16a34a, #4ade80)" }
                        }).showToast();
                    } else {
                        Swal.fire('Error', res.message || 'Validation failed', 'error');
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('Error', 'Server error occurred', 'error');
                });
            });
        });

        // Repeater functionality for dynamic add/remove
        document.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-row');
            if (removeBtn) {
                removeBtn.closest('.repeater-row').remove();
            }
        });

        // Add row handler
        document.querySelectorAll('.add-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const container = document.querySelector(this.dataset.container);
                if (!container) return;
                const template = container.querySelector('.repeater-template');
                if (!template) return;
                const index = container.querySelectorAll('.repeater-row').length;
                const html = template.innerHTML.replace(/\{\{index\}\}/g, index);
                container.insertAdjacentHTML('beforeend', html);
            });
        });

        // Auto-fill full_name on personal tab
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');
        const fullNameInput = document.getElementById('full_name');
        if (firstNameInput && lastNameInput && fullNameInput) {
            let fullNameManuallyEdited = false;
            fullNameInput.addEventListener('input', function() { fullNameManuallyEdited = true; });
            function autoFillFullName() {
                if (!fullNameManuallyEdited) {
                    fullNameInput.value = [firstNameInput.value.trim(), lastNameInput.value.trim()].filter(Boolean).join(' ');
                }
            }
            firstNameInput.addEventListener('input', autoFillFullName);
            lastNameInput.addEventListener('input', autoFillFullName);
        }
    </script>
    @endpush
</x-app-layout>