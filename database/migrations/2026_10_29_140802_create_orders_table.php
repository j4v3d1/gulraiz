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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('plan');
            $table->string('status')->default('pending_payment')->index();
            $table->string('subscription_type')->nullable()->unique();
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->timestamp('commitment_accepted_at');
            $table->timestamp('commitment_ends_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->string('live_url')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('checklist')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
