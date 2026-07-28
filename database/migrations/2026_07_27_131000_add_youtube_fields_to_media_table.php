<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->boolean('publish_to_youtube')->default(false)->after('is_public');
            $table->string('youtube_format')->nullable()->after('publish_to_youtube');
            $table->string('youtube_status')->default('not_requested')->after('youtube_format');
            $table->string('youtube_title')->nullable()->after('youtube_status');
            $table->text('youtube_description')->nullable()->after('youtube_title');
            $table->string('youtube_video_id')->nullable()->after('youtube_description');
            $table->string('youtube_video_url')->nullable()->after('youtube_video_id');
            $table->text('youtube_last_error')->nullable()->after('youtube_video_url');
            $table->timestamp('youtube_publish_requested_at')->nullable()->after('youtube_last_error');
            $table->timestamp('youtube_published_at')->nullable()->after('youtube_publish_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn([
                'publish_to_youtube',
                'youtube_format',
                'youtube_status',
                'youtube_title',
                'youtube_description',
                'youtube_video_id',
                'youtube_video_url',
                'youtube_last_error',
                'youtube_publish_requested_at',
                'youtube_published_at',
            ]);
        });
    }
};
