<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webauthn_credentials', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Polymorphic relation (User)
            $table->string('authenticatable_type');
            $table->uuid('authenticatable_id');

            // WebAuthn data
            $table->string('credential_id')->unique(); // 🔥 THIS WAS MISSING
            $table->string('rp_id');
            $table->string('alias')->nullable();
            $table->unsignedBigInteger('counter')->default(0);

            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id']);
            $table->index('rp_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webauthn_credentials');
    }
};
