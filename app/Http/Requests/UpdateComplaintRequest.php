<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        $complaint = $this->route('complaint');
        return $complaint && $this->user() && $this->user()->can('update', $complaint);
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:100'],
            'issue_category' => ['required', 'string', 'max:100'],
            'expected_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'received_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'incident_date' => ['nullable', 'date'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'preferred_language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
            'generated_draft' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', 'string', 'in:draft,submitted,resolved,cancelled'],
        ];
    }
}
