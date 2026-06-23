<?php

namespace Modules\Notice\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id'       => 'nullable|integer|exists:branches,id',
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'notice_type'     => 'required|in:General,HR,Holiday,Attendance,Payroll,Policy,Training,Event,Emergency',
            'priority'        => 'required|in:Low,Medium,High,Urgent',
            'publish_date'    => 'required|date',
            'expiry_date'     => 'nullable|date|after_or_equal:publish_date',
            'target_type'     => 'required|in:All,Department,Designation,Branch,Employee',
            'attachment'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'is_popup'        => 'nullable|boolean',
            'is_pinned'       => 'nullable|boolean',
            'is_active'       => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Notice title is required.',
            'title.max'               => 'Notice title cannot exceed 255 characters.',
            'description.required'    => 'Notice description is required.',
            'notice_type.required'    => 'Notice type is required.',
            'notice_type.in'          => 'Please select a valid notice type.',
            'priority.required'       => 'Priority is required.',
            'priority.in'             => 'Please select a valid priority level.',
            'publish_date.required'   => 'Publish date is required.',
            'publish_date.date'       => 'Please provide a valid publish date.',
            'expiry_date.date'        => 'Please provide a valid expiry date.',
            'expiry_date.after_or_equal' => 'Expiry date must be on or after the publish date.',
            'target_type.required'    => 'Target type is required.',
            'target_type.in'          => 'Please select a valid target type.',
            'attachment.file'         => 'Please upload a valid file.',
            'attachment.mimes'        => 'Accepted file types: jpg, png, pdf, doc, docx.',
            'attachment.max'          => 'File size must not exceed 5MB.',
        ];
    }
}
