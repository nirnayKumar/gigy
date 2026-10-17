<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'earnings_entry_id' => ['nullable', 'integer', 'exists:earnings_entries,id'],
            'company_name' => ['required', 'string', 'max:100'],
            'issue_category' => ['required', 'string', 'max:100'],
            'expected_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'received_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'incident_date' => ['nullable', 'date'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'preferred_language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
            'generated_draft' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'string', 'in:draft,submitted,resolved,cancelled'],
        ];
    }
}
