<x-app-layout>
    
    <div class="p-4">
        <div class="bg-white shadow-md rounded-lg">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50/50">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-code-branch text-blue-600"></i>
                    <span class="font-bold text-gray-800 tracking-tight">Branch Management</span>
                </div>
                <button id="btnAddBranch"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow-sm transition-all duration-200 flex items-center gap-2">
                    <i class="fa fa-plus-circle"></i> Add New Branch
                </button>
            </div>

            <div class="p-6">
                <table id="branchTable" class="w-full border-collapse border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border-b text-left text-xs font-bold text-gray-600 uppercase">Company</th>
                            <th class="px-4 py-3 border-b text-left text-xs font-bold text-gray-600 uppercase">Code</th>
                            <th class="px-4 py-3 border-b text-left text-xs font-bold text-gray-600 uppercase">Branch Name</th>
                            <th class="px-4 py-3 border-b text-left text-xs font-bold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-3 border-b text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 border-b text-left text-xs font-bold text-gray-600 uppercase">Created At</th>
                            <th class="px-4 py-3 border-b text-center text-xs font-bold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-branch-form" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-[999] backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Add New Branch</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-circle-xmark text-2xl"></i>
                </button>
            </div>

            <form id="branchForm" class="p-6">
                <input type="hidden" name="id" id="branch_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <select name="company_id" id="company_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border">
                            <option value="">Select Company</option>
                            @foreach(\Modules\Company\Models\Company::all() as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Branch Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" placeholder="e.g. BR-DHAKA" class="w-full border-gray-300 rounded-lg p-2.5 border">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Branch Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" placeholder="Branch Name" class="w-full border-gray-300 rounded-lg p-2.5 border">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" id="phone" placeholder="Phone Number" class="w-full border-gray-300 rounded-lg p-2.5 border">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" placeholder="Email Address" class="w-full border-gray-300 rounded-lg p-2.5 border">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                        <textarea name="address" id="address" rows="2" placeholder="Full Address" class="w-full border-gray-300 rounded-lg p-2.5 border"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">City</label>
                        <input type="text" name="city" id="city" placeholder="City" class="w-full border-gray-300 rounded-lg p-2.5 border">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Country</label>
                        <input type="text" name="country" id="country" value="Bangladesh" class="w-full border-gray-300 rounded-lg p-2.5 border">
                    </div>
                </div>

                <div class="mt-6 flex items-center space-x-8 p-4 bg-blue-50/50 rounded-lg border border-blue-100">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_head_office" id="is_head_office" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-bold text-gray-700">Mark as Head Office</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm font-bold text-gray-700">Active Status</span>
                    </label>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-lg shadow-lg flex items-center gap-2 transition-all">
                        <i class="fa fa-save"></i> <span id="modalButtonText">Save Branch</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // ১. গ্লোবাল মোডাল ক্লোজ
        function closeModal() {
            $('#modal-branch-form').addClass('hidden');
            $('#branchForm')[0].reset();
            $('#branch_id').val('');
            $('#modalTitle').text('Add New Branch');
            $('#modalButtonText').text('Save Branch');
            $('#is_head_office').prop('checked', false);
            $('#is_active').prop('checked', true);
        }

        $(document).ready(function() {
        
            
            var dataTable = $('#branchTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('branches.index') }}",
                columns: [
                    { data: 'company.name', name: 'company.name' },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'is_head_office', name: 'is_head_office' },
                    { data: 'is_active', name: 'is_active' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                dom: '<"flex flex-wrap justify-between items-center mb-4"lBf>rt<"flex justify-between items-center mt-4"ip>',
                buttons: ['copy', 'excel', 'pdf', 'print']

                
            });

            // ৩. অ্যাড মোডাল ওপেন
            $('#btnAddBranch').click(function() {
                closeModal();
                $('#modal-branch-form').removeClass('hidden');
            });

            // ৪. সেভ এবং আপডেট (jQuery Submit)
            $('#branchForm').submit(function(e) {
                e.preventDefault();
                
                let branchId = $('#branch_id').val();
                let formData = $(this).serialize();
                let url = branchId ? '/branches/' + branchId : "{{ route('branches.store') }}";
                
                // রিসোর্স রাউট স্পুফিং (PUT Method)
                if(branchId) formData += '&_method=PUT';

                Swal.fire({
                    title: 'Are you sure?',
                    text: branchId ? "Update existing branch info?" : "Create a new branch?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: branchId ? 'Yes, Update' : 'Yes, Save'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: formData,
                            success: function(res) {
                                if (res.status === true) {
                                    Swal.fire('Success!', res.message, 'success');
                                    closeModal();
                                    dataTable.ajax.reload();
                                } else if (res.status === 'validation-error') {
                                    let err = Object.values(res.data).map(v => v[0]).join('<br>');
                                    Swal.fire('Validation Error', err, 'error');
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            },
                            error: () => Swal.fire('Error', 'Internal Server Error!', 'error')
                        });
                    }
                });
            });
        });

        // ৫. এডিট ফাংশন (GET /branches/{id})
        function branchEdit(id) {
            $.get('/branches/' + id, function(res) {
                if (res.status) {
                    const d = res.data;
                    $('#branch_id').val(d.id);
                    $('#company_id').val(d.company_id);
                    $('#code').val(d.code);
                    $('#name').val(d.name);
                    $('#phone').val(d.phone);
                    $('#email').val(d.email);
                    $('#address').val(d.address);
                    $('#city').val(d.city);
                    $('#country').val(d.country);
                    $('#is_head_office').prop('checked', d.is_head_office == 1);
                    $('#is_active').prop('checked', d.is_active == 1);

                    $('#modalTitle').text('Update Branch');
                    $('#modalButtonText').text('Update Branch');
                    $('#modal-branch-form').removeClass('hidden');
                }
            });
        }

        // ৬. ডিলিট ফাংশন (DELETE /branches/{id})
        function branchDelete(id) {
            Swal.fire({
                title: 'Delete Branch?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/branches/' + id,
                        data: { _method: 'DELETE' },
                        success: function(res) {
                            if (res.status === true) {
                                Swal.fire('Deleted!', res.message, 'success');
                                $('#branchTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Denied', res.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>