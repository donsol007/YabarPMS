<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage settings');
    }

    public function rules(): array
    {
        return [
            'mail_host' => ['required', 'string', 'max:191'],
            'mail_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:191'],
            'mail_password' => ['nullable', 'string', 'max:191'],
            'mail_encryption' => ['required', 'in:none,tls,ssl'],
            'mail_from_address' => ['required', 'email', 'max:191'],
            'mail_from_name' => ['nullable', 'string', 'max:191'],
        ];
    }
}