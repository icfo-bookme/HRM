<?php

namespace Modules\Salary\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeSalaryStructureRequest extends FormRequest
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
            'employee_id'    => 'required|integer|exists:employees,id',
            'component_id'   => 'required|integer|exists:salary_components,id',
            'amount'         => 'required|numeric|min:0|max:99999999.9999',
            'effective_from' => 'required|date',
            'effective_to'   => 'nullable|date|after_or_equal:effective_from',
            'is_percentage'  => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'employee_id.required'     => 'Employee is required.',
            'employee_id.exists'       => 'The selected employee does not exist.',
            'component_id.required'    => 'Salary component is required.',
            'component_id.exists'      => 'The selected component does not exist.',
            'amount.required'          => 'Amount is required.',
            'amount.numeric'           => 'Amount must be a number.',
            'amount.min'               => 'Amount cannot be negative.',
            'amount.max'               => 'Amount is too large.',
            'effective_from.required'  => 'Effective from date is required.',
            'effective_from.date'      => 'Effective from must be a valid date.',
            'effective_to.date'        => 'Effective to must be a valid date.',
            'effective_to.after_or_equal' => 'Effective to must be after or equal to effective from.',
        ];
    }
}