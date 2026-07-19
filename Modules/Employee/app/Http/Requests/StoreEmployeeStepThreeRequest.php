<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepThreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // =========================
            // PRESENT ADDRESS
            // =========================
            'present_address' => 'nullable|array',

            'present_address.address_type' => 'nullable|in:present',
            'present_address.house_no' => 'nullable|string|max:100',
            'present_address.road_no' => 'nullable|string|max:100',
            'present_address.road_name' => 'nullable|string|max:150',
            'present_address.village' => 'nullable|string|max:150',
            'present_address.area' => 'nullable|string|max:150',
            'present_address.post_office' => 'nullable|string|max:150',
            'present_address.postal_code' => 'nullable|string|max:20',

            'present_address.city' => 'nullable|string|max:100',
            'present_address.upazila' => 'nullable|string|max:100',
            'present_address.district' => 'nullable|string|max:100',
            'present_address.division' => 'nullable|string|max:100',

            'present_address.state' => 'nullable|string|max:100',
            'present_address.country' => 'nullable|string|max:100',

            'present_address.latitude' => 'nullable|numeric',
            'present_address.longitude' => 'nullable|numeric',

            // =========================
            // PERMANENT ADDRESS
            // =========================
            'permanent_address' => 'nullable|array',

            'permanent_address.address_type' => 'nullable|in:permanent',
            'permanent_address.house_no' => 'nullable|string|max:100',
            'permanent_address.road_no' => 'nullable|string|max:100',
            'permanent_address.road_name' => 'nullable|string|max:150',
            'permanent_address.village' => 'nullable|string|max:150',
            'permanent_address.area' => 'nullable|string|max:150',
            'permanent_address.post_office' => 'nullable|string|max:150',
            'permanent_address.postal_code' => 'nullable|string|max:20',

            'permanent_address.city' => 'nullable|string|max:100',
            'permanent_address.upazila' => 'nullable|string|max:100',
            'permanent_address.district' => 'nullable|string|max:100',
            'permanent_address.division' => 'nullable|string|max:100',

            'permanent_address.state' => 'nullable|string|max:100',
            'permanent_address.country' => 'nullable|string|max:100',

            'permanent_address.latitude' => 'nullable|numeric',
            'permanent_address.longitude' => 'nullable|numeric',
        ];
    }
}