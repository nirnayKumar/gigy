<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'worker_type' => ['nullable', 'string', 'max:100'],
            'preferred_language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your full name.',
            'email.required' => 'An email address is required.',
            'email.unique' => 'This email is already registered. Please log in instead.',
            'password.required' => 'A secure password of at least 8 characters is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'preferred_language.in' => 'Please select a supported language (English, Hindi, or Punjabi).',
        ];
    }
}
