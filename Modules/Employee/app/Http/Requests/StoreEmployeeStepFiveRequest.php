<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeStepFiveRequest extends FormRequest
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
            'documents' => [
                'required',
                'array',
                'min:1',
            ],

            'documents.*.category' => [
                'required',
                'string',
                'max:500',
            ],

            'documents.*.document_name' => [
                'nullable',
                'string',
                'max:300',
            ],

            'documents.*.document_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120', // 5MB
            ],

            'documents.*.document_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'documents.*.issuing_authority' => [
                'nullable',
                'string',
                'max:300',
            ],

            'documents.*.issue_date' => [
                'nullable',
                'date',
            ],

            'documents.*.expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'documents.*.notes' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.required' => 'Please add at least one document.',

            'documents.*.category.required' =>
                'Document category is required.',

            'documents.*.document_file.required' =>
                'Please upload a document file.',

            'documents.*.document_file.mimes' =>
                'Allowed file types: PDF, JPG, JPEG, PNG, WEBP.',

            'documents.*.document_file.max' =>
                'Document size cannot exceed 5 MB.',
        ];
    }
}