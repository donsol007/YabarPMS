<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage settings');
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:191'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'due_notice_days' => ['required', 'integer', 'min:1', 'max:90'],
            'upload_max_size' => ['required', 'integer', 'min:1', 'max:50'],
            'company_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'company_favicon' => ['nullable', 'image', 'mimes:png,ico,svg', 'max:512'],
        ];
    }
}