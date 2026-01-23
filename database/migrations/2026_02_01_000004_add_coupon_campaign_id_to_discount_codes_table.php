<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->foreignId('coupon_campaign_id')
                ->nullable()
                ->after('id')
                ->constrained('coupon_campaigns')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_campaign_id');
        });
    }
};
