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
            $table->renameColumn('name', 'title');
            $table->decimal('total_amount', 10, 2)->nullable()->after('title');
            $table->decimal('coupon_value', 10, 2)->nullable()->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('coupon_campaigns', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->dropColumn(['total_amount', 'coupon_value']);
        });
    }
};
