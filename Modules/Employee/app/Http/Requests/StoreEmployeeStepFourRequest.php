<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepFourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'nullable|string|max:200',
            'bank_branch' => 'nullable|string|max:200',
            'bank_account' => 'nullable|string|max:80',
            'bank_routing' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'mfs_type' => 'nullable|in:bKash,Nagad,Rocket,Upay,Others',
            'mfs_number' => 'nullable|string|max:20',
            'payment_method' => 'nullable|in:Bank,Cash,MFS,Cheque',
            'is_primary' => 'nullable|boolean',
            'verification_status' => 'nullable|in:Pending,Verified,Rejected',
            'verified_at' => 'nullable|date',
            'verified_by' => 'nullable|integer|exists:employees,id',
        ];
    }
}
