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
        Schema::create('intake_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('desired_domain')->nullable();
            $table->text('about_text');
            $table->text('services_text')->nullable();
            $table->string('colour_preferences')->nullable();
            $table->text('extra_notes')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('photo_paths')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intake_submissions');
    }
};
