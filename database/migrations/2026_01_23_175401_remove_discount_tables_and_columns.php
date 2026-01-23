<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            if (Schema::hasColumn('orders', 'discount_code_id')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('discount_code_id');
                });
            }

            if (Schema::hasColumn('orders', 'discount_type')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropColumn(['discount_type', 'discount_value', 'discount_amount']);
                });
            }
        }

        if (Schema::hasTable('discount_codes')) {
            Schema::drop('discount_codes');
        }
    }

    public function down(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // percent, fixed
            $table->decimal('value', 10, 2);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('unused');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('discount_code_id')->nullable()->after('service_charge')->constrained('discount_codes')->nullOnDelete();
            $table->string('discount_type')->nullable()->after('service_charge'); // percent, fixed
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_value');
        });
    }
};
