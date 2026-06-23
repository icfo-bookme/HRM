<?php

namespace Modules\Branch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
{
    public function rules(): array
    {
        
        $branchId = $this->route('branch'); 
       
        return [
            'company_id'     => 'nullable|integer|exists:companies,id',
            'code'           => [
                'required',
                'string',
                'max:20',
                Rule::unique('branches')->where(function ($query) {
                    return $query->where('company_id', $this->company_id);
                })->ignore($branchId)
            ],
            'name'           => 'required|string|max:200',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'zip_code'       => 'nullable|string|max:20',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'is_head_office' => 'nullable|boolean',
            'is_active'      => 'nullable|boolean',
            'metadata'       => 'nullable|array',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
