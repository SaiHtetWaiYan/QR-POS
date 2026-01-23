<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_code_id')->nullable()->after('service_charge')->constrained('coupon_codes')->nullOnDelete();
            $table->string('coupon_type')->nullable()->after('service_charge'); // percent, fixed
            $table->decimal('coupon_value', 10, 2)->nullable()->after('coupon_type');
            $table->decimal('coupon_amount', 10, 2)->default(0)->after('coupon_value');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_code_id');
            $table->dropColumn(['coupon_type', 'coupon_value', 'coupon_amount']);
        });
    }
};
