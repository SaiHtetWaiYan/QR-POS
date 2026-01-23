<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_symbol' => 'required|string|max:5',
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'nullable|string|max:255',
            'shop_phone' => 'nullable|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'service_charge' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'currency_symbol.required' => 'Currency symbol is required.',
            'currency_symbol.max' => 'Currency symbol must be 5 characters or less.',
            'shop_name.required' => 'Shop name is required.',
            'tax_rate.required' => 'Tax rate is required.',
            'tax_rate.numeric' => 'Tax rate must be a number.',
            'tax_rate.min' => 'Tax rate cannot be negative.',
            'tax_rate.max' => 'Tax rate cannot exceed 100%.',
            'service_charge.required' => 'Service charge is required.',
            'service_charge.numeric' => 'Service charge must be a number.',
            'service_charge.min' => 'Service charge cannot be negative.',
            'service_charge.max' => 'Service charge cannot exceed 100%.',
        ];
    }
}
