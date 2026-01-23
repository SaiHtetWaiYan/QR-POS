<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function edit()
    {
        $currencySymbol = Setting::where('key', 'currency_symbol')->value('value')
            ?? config('pos.currency_symbol', '$');
        $shopName = Setting::where('key', 'shop_name')->value('value')
            ?? config('pos.shop_name', config('app.name', 'QR POS'));
        $shopAddress = Setting::where('key', 'shop_address')->value('value')
            ?? config('pos.shop_address', '');
        $shopPhone = Setting::where('key', 'shop_phone')->value('value')
            ?? config('pos.shop_phone', '');

        $taxSetting = Setting::where('key', 'tax_rate')->first();
        $serviceSetting = Setting::where('key', 'service_charge')->first();

        $taxRate = $taxSetting
            ? (float) $taxSetting->value * 100
            : (float) config('pos.tax_rate', 0) * 100;

        $serviceCharge = $serviceSetting
            ? (float) $serviceSetting->value * 100
            : (float) config('pos.service_charge', 0) * 100;

        return view('pos.settings', compact('currencySymbol', 'shopName', 'shopAddress', 'shopPhone', 'taxRate', 'serviceCharge'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        $data = $request->validated();
        $taxRate = $data['tax_rate'] / 100;
        $serviceCharge = $data['service_charge'] / 100;

        Setting::updateOrCreate(
            ['key' => 'tax_rate'],
            ['value' => number_format($taxRate, 4, '.', '')]
        );

        Setting::updateOrCreate(
            ['key' => 'service_charge'],
            ['value' => number_format($serviceCharge, 4, '.', '')]
        );

        Setting::updateOrCreate(
            ['key' => 'currency_symbol'],
            ['value' => trim($data['currency_symbol'])]
        );

        Setting::updateOrCreate(
            ['key' => 'shop_name'],
            ['value' => trim($data['shop_name'])]
        );

        Setting::updateOrCreate(
            ['key' => 'shop_address'],
            ['value' => trim((string) ($data['shop_address'] ?? ''))]
        );

        Setting::updateOrCreate(
            ['key' => 'shop_phone'],
            ['value' => trim((string) ($data['shop_phone'] ?? ''))]
        );

        config([
            'pos.tax_rate' => $taxRate,
            'pos.service_charge' => $serviceCharge,
            'pos.currency_symbol' => trim($data['currency_symbol']),
            'pos.shop_name' => trim($data['shop_name']),
            'pos.shop_address' => trim((string) ($data['shop_address'] ?? '')),
            'pos.shop_phone' => trim((string) ($data['shop_phone'] ?? '')),
        ]);

        return back()->with('success', 'Settings updated.');
    }
}
