<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEarningsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'work_date' => ['required', 'date', 'before_or_equal:today'],
            'gross_earnings' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'incentives' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'fuel_expenses' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'other_expenses' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'platform_deductions' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'hours_worked' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'work_date.required' => 'The shift date is required.',
            'work_date.before_or_equal' => 'Shift date cannot be in the future.',
            'gross_earnings.required' => 'Please enter the base gross earnings.',
            'gross_earnings.min' => 'Gross earnings cannot be negative.',
            'hours_worked.required' => 'Please specify the hours worked during the shift.',
            'hours_worked.min' => 'Logged hours must be at least 0.1 hours (6 minutes).',
            'hours_worked.max' => 'Shift hours cannot exceed 24 hours in a single entry.',
        ];
    }
}
