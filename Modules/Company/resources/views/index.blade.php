<x-app-layout>
    <div class="p-4">
        <x-data-table id="companyTable" title="Company Management" icon="fa-solid fa-building" buttonId="btnAddCompany"
            buttonText="Add New Company" :columns="['Name', 'Legal Name', 'City', 'Phone', 'Email', 'Status', 'Created At', 'Action']" :ajaxUrl="route('company.dataTable')" :dtColumns="[
                ['data' => 'name'],
                ['data' => 'legal_name'],
                ['data' => 'city'],
                ['data' => 'phone'],
                ['data' => 'email'],
                ['data' => 'is_active'],
                ['data' => 'created_at'],
                ['data' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    <x-drawer id="company-drawer" overlayId="company-overlay" title="Add New Company" submitOnClick="saveCompany()">
        <form id="companyForm">
            <input type="hidden" id="company_id" name="id">

            <div class="grid gap-4 md:grid-cols-2">
                <x-form-input label="Name" name="name" id="name" placeholder="Company name" />
                <x-form-input label="Legal Name" name="legal_name" id="legal_name" placeholder="Legal name" />
                <x-form-input label="Trade License" name="trade_license" id="trade_license" placeholder="Trade license" />
                <x-form-input label="BIN Number" name="bin_number" id="bin_number" placeholder="BIN" />
                <x-form-input label="TIN Number" name="tin_number" id="tin_number" placeholder="TIN" />
                <x-form-input label="Industry" name="industry" id="industry" placeholder="Industry" />
                <x-form-input label="Founded Year" name="founded_year" id="founded_year" type="number" placeholder="2020" />
                <x-form-input label="Phone" name="phone" id="phone" placeholder="Phone" />
                <x-form-input label="Email" name="email" id="email" type="email" placeholder="info@company.com" />
                <x-form-input label="Website" name="website" id="website" placeholder="https://example.com" />
                <x-form-input label="City" name="city" id="city" placeholder="City" />
                <x-form-select label="Country" name="country" id="country" placeholder="Country">
                    <option value="">-- Select Country --</option>
                    <option value="Bangladesh">Bangladesh</option>
                </x-form-select>
            </div>

            <div class="mt-4">
                <x-form-textarea label="Address" name="address" id="address" placeholder="Address" rows="3" />
            </div>

            <div class="space-y-2 bg-slate-50 p-3 rounded-md border border-slate-100 mt-4">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Active</span>
                </label>
            </div>
        </form>
    </x-drawer>

    @push('scripts')
        <script>
            let isSavingCompany = false;
            const csrfTokenCompany = '{{ csrf_token() }}';

            function openCompanyDrawer(mode = 'add') {
                if (mode === 'edit') {
                    $('#drawerTitle').text('Update Company');
                    $('#drawerButtonText').text('Update Company');
                } else {
                    resetCompanyForm();
                    $('#drawerTitle').text('Add New Company');
                    $('#drawerButtonText').text('Save Company');
                }
                openGlobalDrawer('company-drawer', 'company-overlay');
            }

            function resetCompanyForm() {
                $('#companyForm')[0].reset();
                $('#company_id').val('');
                $('#is_active').prop('checked', true);
                isSavingCompany = false;
            }

            function companyEdit(id) {
                Swal.fire({ title: 'Loading...', text: 'Fetching company details', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                resetCompanyForm();
                $.get("{{ route('company.show', ':id') }}".replace(':id', id), function (res) {
                    if (res.status === 'success') {
                        const c = res.company;
                        $('#company_id').val(c.id);
                        $('#name').val(c.name);
                        $('#legal_name').val(c.legal_name);
                        $('#trade_license').val(c.trade_license);
                        $('#bin_number').val(c.bin_number);
                        $('#tin_number').val(c.tin_number);
                        $('#industry').val(c.industry);
                        $('#founded_year').val(c.founded_year);
                        $('#phone').val(c.phone);
                        $('#email').val(c.email);
                        $('#website').val(c.website);
                        $('#city').val(c.city);
                        $('#country').val(c.country);
                        $('#address').val(c.address);
                        $('#is_active').prop('checked', c.is_active == 1);
                        $('#drawerTitle').text('Update Company');
                        $('#drawerButtonText').text('Update Company');
                        Swal.close();
                        openCompanyDrawer('edit');
                    } else {
                        Swal.fire('Error', res.message || 'Unable to fetch company.');
                    }
                }).fail(function () { Swal.fire('Error', 'Server communication error.'); });
            }

            function saveCompany() {
                if (isSavingCompany) return;
                const companyId = $('#company_id').val();
                let url = companyId ? "{{ route('company.update', ':id') }}".replace(':id', companyId) : "{{ route('company.store') }}";
                let formData = $('#companyForm').serialize();
                if (companyId) formData += '&_method=PUT';
                formData += '&_token=' + csrfTokenCompany;

                isSavingCompany = true;
                $('#drawerButtonText').text('Saving...');
                $('#saveBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                $.ajax({ url: url, method: 'POST', data: formData,
                    success: function (res) {
                        isSavingCompany = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        if (res.status === 'success') {
                            Toastify({ text: res.message, duration: 3000, gravity: 'bottom', position: 'right', backgroundColor: 'linear-gradient(135deg, #16a34a, #4ade80)', }).showToast();
                            closeGlobalDrawer('company-drawer', 'company-overlay');
                            $('#companyTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Could not save company.');
                            $('#drawerButtonText').text(companyId ? 'Update Company' : 'Save Company');
                        }
                    },
                    error: function (xhr) {
                        isSavingCompany = false;
                        $('#saveBtn').prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                        $('#drawerButtonText').text(companyId ? 'Update Company' : 'Save Company');
                        const errors = xhr.responseJSON?.errors;
                        const message = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Server error occurred.');
                        Swal.fire({ icon: 'error', title: 'Error', html: message });
                    }
                });
            }

            function companyDelete(id) {
                Swal.fire({ title: 'Delete Company?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280' }).then(result => {
                    if (!result.isConfirmed) return;
                    $.post("{{ route('company.destroy', ':id') }}".replace(':id', id), { _method: 'DELETE', _token: csrfTokenCompany }, function (res) {
                        if (res.status === 'success') { Swal.fire('Deleted', res.message, 'success'); $('#companyTable').DataTable().ajax.reload(null, false); } else { Swal.fire('Error', res.message || 'Unable to delete company.'); }
                    }).fail(function () { Swal.fire('Error', 'Server communication error.'); });
                });
            }

            $(document).ready(function () { $(document).on('click', '#btnAddCompany', function () { openCompanyDrawer('add'); }); });
        </script>
    @endpush
</x-app-layout>
