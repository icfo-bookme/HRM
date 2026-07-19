<?php

namespace Modules\Salary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryComponentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:200',
            'type'               => 'required|in:Earning,Deduction,Reimbursement,Bonus',
            'category'           => 'nullable|in:Basic,Allowance,Bonus,PF,Tax,Insurance,Loan,Other',
            'calculation_type'   => 'nullable|in:Fixed,Percentage of Basic,Percentage of Gross,Formula,Custom',
            'default_value'      => 'nullable|numeric|min:0|max:99999999.9999',
            'formula_expression' => 'nullable|string|max:1000',
            'is_taxable'         => 'nullable|boolean',
            'is_pf_basis'        => 'nullable|boolean',
            'is_active'          => 'nullable|boolean',
            'show_in_slip'       => 'nullable|boolean',
            'display_order'      => 'nullable|integer|min:0|max:999',
            'metadata'           => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required'            => 'Component name is required.',
            'name.max'                 => 'Component name cannot exceed 200 characters.',
            'type.required'            => 'Component type is required.',
            'type.in'                  => 'Type must be one of: Earning, Deduction, Reimbursement, Bonus.',
            'category.in'              => 'Category must be one of: Basic, Allowance, Bonus, PF, Tax, Insurance, Loan, Other.',
            'calculation_type.in'      => 'Calculation type is invalid.',
            'default_value.numeric'    => 'Default value must be a number.',
            'default_value.min'        => 'Default value cannot be negative.',
            'default_value.max'        => 'Default value is too large.',
            'formula_expression.max'   => 'Formula expression cannot exceed 1000 characters.',
            'display_order.integer'    => 'Display order must be an integer.',
            'display_order.min'        => 'Display order cannot be negative.',
            'display_order.max'        => 'Display order cannot exceed 999.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('metadata') && is_string($this->metadata)) {
            $this->merge([
                'metadata' => json_decode($this->metadata, true),
            ]);
        }
    }
}