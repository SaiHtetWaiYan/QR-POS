<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupon_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('total_codes')->default(0)->after('code_length');
        });
    }

    public function down(): void
    {
        Schema::table('coupon_campaigns', function (Blueprint $table) {
            $table->dropColumn('total_codes');
        });
    }
};
