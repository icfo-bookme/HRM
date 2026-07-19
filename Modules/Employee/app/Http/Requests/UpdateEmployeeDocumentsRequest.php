<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documents' => ['nullable', 'array'],
            'documents.*.category' => ['required', 'string'],
            'documents.*.document_name' => ['nullable', 'string', 'max:255'],
            'documents.*.document_number' => ['nullable', 'string', 'max:100'],
            'documents.*.issuing_authority' => ['nullable', 'string', 'max:255'],
            'documents.*.issue_date' => ['nullable', 'date'],
            'documents.*.expiry_date' => ['nullable', 'date'],
            'documents.*.notes' => ['nullable', 'string'],
        ];
    }
}