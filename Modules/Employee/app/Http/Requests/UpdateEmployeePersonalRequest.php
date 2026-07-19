<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeePersonalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'     => ['required', 'string', 'max:150'],
            'last_name'      => ['required', 'string', 'max:150'],
            'full_name'      => ['required', 'string', 'max:300'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'phone_2'        => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:200'],
            'date_of_birth'  => ['nullable', 'date'],
            'gender'         => ['nullable', 'string', 'max:50'],
            'nationality'    => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'blood_group'    => ['nullable', 'string', 'max:10'],
            'father_name'    => ['nullable', 'string', 'max:200'],
            'mother_name'    => ['nullable', 'string', 'max:200'],
            'spouse_name'    => ['nullable', 'string', 'max:200'],
            'personal_email' => ['nullable', 'email', 'max:200'],
            'personal_mobile'=> ['nullable', 'string', 'max:20'],
            'religion'       => ['nullable', 'string', 'max:80'],
        ];
    }
}