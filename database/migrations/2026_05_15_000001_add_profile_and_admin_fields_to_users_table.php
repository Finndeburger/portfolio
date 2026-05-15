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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender', 32)->default('prefer_not_to_say')->after('name');
            $table->string('profile_picture')->default('assets/general/pfpnb.png')->after('gender');
            $table->string('role', 32)->default('user')->after('remember_token')->index();
            $table->json('passkey_credentials')->nullable()->after('role');
            $table->timestamp('passkey_enabled_at')->nullable()->after('passkey_credentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'profile_picture',
                'role',
                'passkey_credentials',
                'passkey_enabled_at',
            ]);
        });
    }
};
