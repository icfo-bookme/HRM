<x-app-layout>
    <div class="p-4 lg:p-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Edit Loan Application</h1>
                <p class="text-sm text-gray-500 mt-0.5">Update loan application details</p>
            </div>
            <a href="{{ route('loan.show', $loan->id) }}" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-1.5"></i>Back to Details
            </a>
        </div>

        <div class="max-w-2xl">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Edit Loan #{{ $loan->id }}</h2>
                </div>
                <div class="p-4">
                    <form action="{{ route('loan.update', $loan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Employee</label>
                                <select name="employee_id" required
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ $loan->employee_id == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->employee_code }} - {{ $emp->personalInfo?->full_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Type</label>
                                <select name="loan_type" required
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Personal" {{ $loan->loan_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="Emergency" {{ $loan->loan_type == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                    <option value="Education" {{ $loan->loan_type == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Medical" {{ $loan->loan_type == 'Medical' ? 'selected' : '' }}>Medical</option>
                                    <option value="Vehicle" {{ $loan->loan_type == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                                    <option value="Home" {{ $loan->loan_type == 'Home' ? 'selected' : '' }}>Home</option>
                                    <option value="Other" {{ $loan->loan_type == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Loan Amount</label>
                                <input type="number" name="loan_amount" step="0.01" min="1" required
                                    value="{{ old('loan_amount', $loan->loan_amount) }}"
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Interest Rate (%)</label>
                                <input type="number" name="interest_rate" step="0.01" min="0" max="100"
                                    value="{{ old('interest_rate', $loan->total_installments > 0 && $loan->loan_amount > 0 ? round(($loan->total_interest / $loan->loan_amount) * 100, 2) : 0) }}"
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Installments (Months)</label>
                                <select name="total_installments" required
                                    class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @for($i = 1; $i <= 60; $i++)
                                        <option value="{{ $i }}" {{ $loan->total_installments == $i ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Month' : 'Months' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <input type="text" value="{{ $loan->status }}" readonly
                                    class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Purpose</label>
                            <textarea name="purpose" rows="3"
                                class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">{{ old('purpose', $loan->purpose) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                            <textarea name="notes" rows="2"
                                class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $loan->notes) }}</textarea>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <i class="fas fa-save mr-1.5"></i>Update Application
                            </button>
                            <a href="{{ route('loan.show', $loan->id) }}"
                                class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>