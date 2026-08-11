<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('image_media_id')
                ->nullable()
                ->after('image')
                ->constrained('media')
                ->nullOnDelete();

            $table->foreignId('video_media_id')
                ->nullable()
                ->after('video_link')
                ->constrained('media')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('image_media_id');
            $table->dropConstrainedForeignId('video_media_id');
        });
    }
};
