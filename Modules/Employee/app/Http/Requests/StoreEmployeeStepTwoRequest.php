<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepTwoRequest extends FormRequest
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
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'full_name' => 'required|string|max:300',

            'phone' => 'required|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:200',

            'date_of_birth' => 'required|date',

            'gender' => 'required|in:Male,Female,Other',

            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed,Separated',

            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',

            'nationality' => 'nullable|string|max:100',

            'religion' => 'nullable|string|max:80',

            'father_name' => 'nullable|string|max:200',
            'mother_name' => 'nullable|string|max:200',
            'spouse_name' => 'nullable|string|max:200',

            'personal_email' => 'nullable|email|max:200',
            'personal_mobile' => 'nullable|string|max:20',

            'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png,avif|mimetypes:image/jpeg,image/png,image/avif|max:2048',
            'signature_file' => 'nullable|file|mimes:jpg,jpeg,png,avif|mimetypes:image/jpeg,image/png,image/avif|max:2048',
        ];
    }
}
