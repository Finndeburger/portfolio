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
        Schema::table('sites', function (Blueprint $table) {
            $table->string('database_connection')->nullable()->after('sponsored');
            $table->string('database_table')->nullable()->after('database_connection');
            $table->json('database_meta')->nullable()->after('database_table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['database_connection', 'database_table', 'database_meta']);
        });
    }
};
