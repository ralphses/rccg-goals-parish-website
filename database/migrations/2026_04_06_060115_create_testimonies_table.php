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
        Schema::create('testimonies', function (Blueprint $table) {

            $table->id();

            // Testifier Information
            $table->string('testifier_name'); 
            $table->string('testifier_phone');
            $table->string('testifier_email')->nullable();

            // Testimony
            $table->string('title');
            $table->text('content')->nullable();// Make content nullable for non-text announcements

            // Announcement Preferences
            $table->boolean('announce_in_service')->default(false);
            $table->string('announcement_type')->nullable();

            // Moderation
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();

            // Tracking
            $table->boolean('announced')->default(false);
            $table->timestamp('announced_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonies');
    }
};