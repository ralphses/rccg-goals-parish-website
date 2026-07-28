<?php

namespace Tests\Feature;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\UserRole;
use App\enums\YouTubePublishStatus;
use App\enums\YouTubeVideoFormat;
use App\Jobs\ProcessVideoMediaUpload;
use App\Jobs\PublishMediaToYouTube;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessVideoMediaUploadJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_background_video_upload_stores_public_url_and_queues_youtube(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Queue::fake();
        Storage::disk('local')->put('youtube-sources/2026/07/video.mp4', 'video-binary');

        $media = $this->createMedia([
            'file_path' => null,
            'upload_status' => MediaUploadStatus::QUEUED,
            'upload_queued_at' => now(),
            'publish_to_youtube' => true,
            'youtube_status' => YouTubePublishStatus::NOT_REQUESTED,
        ]);

        (new ProcessVideoMediaUpload($media->id))->handle(app(\App\Services\CloudinaryUploadService::class));

        $media->refresh();

        $this->assertSame(MediaUploadStatus::READY, $media->upload_status);
        $this->assertNotNull($media->file_path);
        $this->assertSame(YouTubePublishStatus::QUEUED, $media->youtube_status);
        Storage::disk('public')->assertExists(\App\Support\MediaUrl::toStoragePath($media->file_path));
        Queue::assertPushed(PublishMediaToYouTube::class);
    }

    public function test_missing_private_source_marks_media_failed_with_clear_error(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $media = $this->createMedia([
            'file_path' => null,
            'upload_status' => MediaUploadStatus::QUEUED,
            'publish_to_youtube' => true,
            'youtube_status' => YouTubePublishStatus::NOT_REQUESTED,
        ]);

        (new ProcessVideoMediaUpload($media->id))->handle(app(\App\Services\CloudinaryUploadService::class));

        $media->refresh();

        $this->assertSame(MediaUploadStatus::FAILED, $media->upload_status);
        $this->assertStringContainsString('Stored source video could not be found', $media->upload_last_error);
        $this->assertSame(YouTubePublishStatus::FAILED, $media->youtube_status);
    }

    private function createMedia(array $overrides): Media
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $media = new Media([
            'title' => 'Queued Video',
            'file_name' => 'video.mp4',
            'file_path' => $overrides['file_path'] ?? 'https://example.test/video.mp4',
            'thumbnail_path' => $overrides['thumbnail_path'] ?? 'https://example.test/thumb.jpg',
            'youtube_source_path' => $overrides['youtube_source_path'] ?? 'youtube-sources/2026/07/video.mp4',
            'upload_status' => $overrides['upload_status'] ?? MediaUploadStatus::QUEUED,
            'upload_last_error' => $overrides['upload_last_error'] ?? null,
            'upload_queued_at' => $overrides['upload_queued_at'] ?? null,
            'upload_completed_at' => $overrides['upload_completed_at'] ?? null,
            'size' => 1024,
            'media_type' => MediaType::VIDEO,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'publish_to_youtube' => $overrides['publish_to_youtube'] ?? true,
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO,
            'youtube_status' => $overrides['youtube_status'] ?? YouTubePublishStatus::NOT_REQUESTED,
            'youtube_title' => 'Queued Video',
        ]);

        $user->media()->save($media);

        return $media;
    }
}
