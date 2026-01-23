<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = DB::table('settings')->pluck('value', 'key');

        if ($settings->has('tax_rate')) {
            config(['pos.tax_rate' => (float) $settings->get('tax_rate')]);
        }

        if ($settings->has('service_charge')) {
            config(['pos.service_charge' => (float) $settings->get('service_charge')]);
        }

        if ($settings->has('currency_symbol')) {
            config(['pos.currency_symbol' => (string) $settings->get('currency_symbol')]);
        }

        if ($settings->has('shop_name')) {
            config(['pos.shop_name' => (string) $settings->get('shop_name')]);
        }

        if ($settings->has('shop_address')) {
            config(['pos.shop_address' => (string) $settings->get('shop_address')]);
        }

        if ($settings->has('shop_phone')) {
            config(['pos.shop_phone' => (string) $settings->get('shop_phone')]);
        }
    }
}
