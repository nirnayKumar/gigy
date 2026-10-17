<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateComplaintDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
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
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Please select or enter the platform / company name.',
            'issue_category.required' => 'Please specify the grievance issue category.',
            'description.required' => 'Please provide a clear description of the dispute or deduction.',
            'description.min' => 'Please describe the issue in at least 10 characters so Gemini AI has enough context.',
        ];
    }
}
