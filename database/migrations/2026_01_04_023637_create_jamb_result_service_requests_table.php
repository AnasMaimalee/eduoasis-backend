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
        Schema::create('jamb_result_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->uuid('service_id');

            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->string('profile_code')->nullable();
            $table->string('registration_number')->nullable();

            // 💰 Pricing snapshot
            $table->decimal('customer_price', 10, 2)->nullable();
            $table->decimal('admin_payout', 10, 2)->nullable();
            $table->decimal('platform_profit', 10, 2)->nullable();

            // 👷 Workflow
            $table->enum('status', [
                'pending',              // user submitted
                'processing',           // admin took job
                'completed',   // admin uploaded result
                'approved',             // super admin approved
                'rejected',             // super admin rejected
            ])->default('pending');

            $table->uuid('taken_by')->nullable();      // admin
            $table->uuid('completed_by')->nullable();  // admin
            $table->uuid('approved_by')->nullable();
            $table->uuid('rejected_by')->nullable();
            $table->text('rejection_reason')->nullable();
            // super admin

            // 📎 Result
            $table->string('result_file')->nullable();
            $table->text('admin_note')->nullable();

            // 💳 Payment flags
            $table->boolean('is_user_charged')->default(false);
            $table->boolean('is_admin_paid')->default(false);
            $table->boolean('is_paid')->default(false);

            $table->timestamps();
        });


    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamb_result_requests');
    }
};
