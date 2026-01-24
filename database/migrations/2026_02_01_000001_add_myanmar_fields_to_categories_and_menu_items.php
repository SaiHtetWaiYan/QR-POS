<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_my')->nullable()->after('name');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('name_my')->nullable()->after('name');
            $table->text('description_my')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name_my');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['name_my', 'description_my']);
        });
    }
};
