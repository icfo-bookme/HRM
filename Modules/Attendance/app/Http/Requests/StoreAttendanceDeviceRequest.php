<?php

namespace Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceDeviceRequest extends FormRequest
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
            'branch_id'         => 'required|integer|exists:branches,id',
            'device_name'       => 'required|string|max:150',
            'device_code'       => 'required|string|max:50|unique:attendance_devices,device_code',
            'device_type'       => 'required|in:Fingerprint,Face,Card,Mobile App,Web,Manual',
            'brand'             => 'nullable|string|max:100',
            'model'             => 'nullable|string|max:100',
            'serial_number'     => 'required|string|max:100|unique:attendance_devices,serial_number',
            'ip_address'        => 'nullable|string|max:45',
            'port'              => 'nullable|string|max:10',
            'communication_type' => 'nullable|in:LAN,WAN,WiFi,Cloud API,USB',
            'firmware_version'  => 'nullable|string|max:50',
            'timezone'          => 'nullable|string|max:100',
            'location'          => 'nullable|string|max:255',
            'last_sync_at'      => 'nullable|date',
            'sync_status'       => 'nullable|in:Online,Offline,Syncing,Error',
            'is_active'         => 'nullable|boolean',
            'notes'             => 'nullable|string',
            'metadata'          => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'branch_id.required'        => 'Branch is required.',
            'branch_id.exists'          => 'The selected branch does not exist.',
            'device_name.required'      => 'Device name is required.',
            'device_code.required'      => 'Device code is required.',
            'device_code.unique'        => 'Device code must be unique.',
            'device_type.required'      => 'Device type is required.',
            'device_type.in'            => 'The selected device type is invalid.',
            'serial_number.required'    => 'Serial number is required.',
            'serial_number.unique'      => 'Serial number must be unique.',
            'communication_type.in'     => 'The selected communication type is invalid.',
            'sync_status.in'            => 'The selected sync status is invalid.',
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