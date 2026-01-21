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
            if (!Schema::hasColumn('users', 'google2fa_secret')) {
                $table->string('google2fa_secret')->nullable();
            }

            if (!Schema::hasColumn('users', 'google2fa_enabled')) {
                $table->boolean('google2fa_enabled')->default(false);
            }

            if (!Schema::hasColumn('users', 'google2fa_recovery_codes')) {
                $table->text('google2fa_recovery_codes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
