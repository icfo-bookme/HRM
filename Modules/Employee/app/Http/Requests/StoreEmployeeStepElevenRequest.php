<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepElevenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->boolean('skip')) {
            return [];
        }

        return [
            'full_name' => 'nullable|string|max:200',
            'relation' => 'nullable|in:Spouse,Son,Daughter,Father,Mother,Brother,Sister,Other',
            'date_of_birth' => 'nullable|date',
            'nid_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:200',
            'occupation' => 'nullable|string|max:200',
            'is_nominee' => 'nullable|boolean',
            'nominee_percent' => 'nullable|numeric|min:0|max:100',
            'priority_order' => 'nullable|integer|min:0|max:255',
        ];
    }
}