<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $conversation = $this->route('conversation');
        return $conversation && $this->user() && $this->user()->can('update', $conversation);
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Please type your question or message before sending.',
            'message.max' => 'Your message cannot exceed 2000 characters.',
        ];
    }
}
