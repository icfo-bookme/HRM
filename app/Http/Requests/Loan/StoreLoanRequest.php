<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loan_type' => 'required|in:Personal,Emergency,Education,Medical,Vehicle,Home,Other',
            'loan_amount' => 'required|numeric|min:1',
            'total_installments' => 'required|integer|min:1|max:120',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'purpose' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'loan_type.required' => 'Please select a loan type.',
            'loan_type.in' => 'Loan type must be: Personal, Emergency, Education, Medical, Vehicle, Home, or Other.',
            'loan_amount.required' => 'Loan amount is required.',
            'loan_amount.numeric' => 'Loan amount must be a number.',
            'loan_amount.min' => 'Loan amount must be at least 1.',
            'total_installments.required' => 'Number of installments is required.',
            'total_installments.min' => 'Minimum installment is 1.',
            'total_installments.max' => 'Maximum installment is 120.',
            'interest_rate.max' => 'Interest rate cannot exceed 100%.',
        ];
    }
}
