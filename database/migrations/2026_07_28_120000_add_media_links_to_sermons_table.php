<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sermons', function (Blueprint $table) {
            $table->foreignId('cover_media_id')->nullable()->after('cover_image')->constrained('media')->nullOnDelete();
            $table->foreignId('audio_media_id')->nullable()->after('audio_url')->constrained('media')->nullOnDelete();
            $table->foreignId('video_media_id')->nullable()->after('video_url')->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sermons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cover_media_id');
            $table->dropConstrainedForeignId('audio_media_id');
            $table->dropConstrainedForeignId('video_media_id');
        });
    }
};
