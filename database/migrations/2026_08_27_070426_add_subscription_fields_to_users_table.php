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
            $table->enum('subscription_tier', ['free', 'premium'])->default('free')->after('password');
            $table->timestamp('premium_expires_at')->nullable()->after('subscription_tier');
            $table->string('avatar')->nullable()->after('premium_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_tier', 'premium_expires_at', 'avatar']);
        });
    }
};
