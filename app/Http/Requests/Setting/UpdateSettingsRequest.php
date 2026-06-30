<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage settings');
    }

    public function rules(): array
    {
        return [
            'settings' => 'required|array',
            'settings.*' => 'nullable', // Various types, we'll accept them and process
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ];
    }
}
