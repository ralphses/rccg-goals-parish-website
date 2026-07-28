<?php

namespace Tests\Feature;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\UserRole;
use App\enums\YouTubePublishStatus;
use App\enums\YouTubeVideoFormat;
use App\Jobs\PublishMediaToYouTube;
use App\Models\Media;
use App\Models\User;
use App\Models\YouTubeIntegration;
use App\Services\YouTubeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublishMediaToYouTubeJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_publish_stores_video_id_url_and_status(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('youtube-sources/2026/07/video.mp4', 'video-binary');
        Storage::disk('public')->put('media/thumb.jpg', 'thumb-binary');

        Http::fake([
            'www.googleapis.com/upload/youtube/v3/videos*' => Http::response('', 200, [
                'Location' => 'https://upload.youtube.test/session/1',
            ]),
            'upload.youtube.test/*' => Http::response([
                'id' => 'yt123',
            ]),
            'www.googleapis.com/upload/youtube/v3/thumbnails/set*' => Http::response([], 200),
        ]);

        $integration = $this->createIntegration();
        $media = $this->createMedia();

        (new PublishMediaToYouTube($media->id))->handle(app(YouTubeService::class));

        $media->refresh();
        $this->assertSame(YouTubePublishStatus::UPLOADED_PRIVATE, $media->youtube_status);
        $this->assertSame('yt123', $media->youtube_video_id);
        $this->assertSame('https://www.youtube.com/watch?v=yt123', $media->youtube_video_url);
        $this->assertNotNull($media->youtube_published_at);
        $integration->refresh();
        $this->assertNull($integration->last_error);
    }

    public function test_refresh_failure_marks_media_failed_with_error(): void
    {
        Storage::fake('local');
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'error' => 'invalid_grant',
            ], 400),
        ]);

        $this->createIntegration(expired: true);
        $media = $this->createMedia();

        try {
            (new PublishMediaToYouTube($media->id))->handle(app(YouTubeService::class));
            $this->fail('Expected publish job to throw on token refresh failure.');
        } catch (\Throwable) {
            $media->refresh();
            $this->assertSame(YouTubePublishStatus::FAILED, $media->youtube_status);
            $this->assertNotNull($media->youtube_last_error);
        }
    }

    public function test_failed_upload_does_not_remove_app_media_record(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('youtube-sources/2026/07/video.mp4', 'video-binary');
        Storage::disk('public')->put('media/thumb.jpg', 'thumb-binary');

        Http::fake([
            'www.googleapis.com/upload/youtube/v3/videos*' => Http::response('', 200, [
                'Location' => 'https://upload.youtube.test/session/1',
            ]),
            'upload.youtube.test/*' => Http::response([
                'error' => 'upload failed',
            ], 500),
        ]);

        $this->createIntegration();
        $media = $this->createMedia();

        try {
            (new PublishMediaToYouTube($media->id))->handle(app(YouTubeService::class));
            $this->fail('Expected publish job to throw on upload failure.');
        } catch (\Throwable) {
            $this->assertDatabaseHas('media', ['id' => $media->id]);
            $media->refresh();
            $this->assertSame(YouTubePublishStatus::FAILED, $media->youtube_status);
            $this->assertTrue(Storage::disk('local')->exists('youtube-sources/2026/07/video.mp4'));
        }
    }

    public function test_missing_private_source_marks_media_failed_with_clear_error(): void
    {
        Storage::fake('local');
        $this->createIntegration();
        $media = $this->createMedia();
        Storage::disk('local')->delete('youtube-sources/2026/07/video.mp4');

        try {
            (new PublishMediaToYouTube($media->id))->handle(app(YouTubeService::class));
            $this->fail('Expected publish job to throw when the private source is missing.');
        } catch (\Throwable) {
            $media->refresh();
            $this->assertSame(YouTubePublishStatus::FAILED, $media->youtube_status);
            $this->assertStringContainsString('Stored YouTube source file could not be found', $media->youtube_last_error);
        }
    }

    private function createIntegration(bool $expired = false): YouTubeIntegration
    {
        return YouTubeIntegration::create([
            'channel_id' => 'UC12345',
            'channel_title' => 'RCCG Goals Parish',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
            'token_expires_at' => $expired ? now()->subMinute() : now()->addHour(),
        ]);
    }

    private function createMedia(): Media
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $media = new Media([
            'title' => 'Queued Video',
            'file_name' => 'video.mp4',
            'file_path' => 'https://example.test/video.mp4',
            'thumbnail_path' => url(Storage::url('media/thumb.jpg')),
            'youtube_source_path' => 'youtube-sources/2026/07/video.mp4',
            'upload_status' => MediaUploadStatus::READY,
            'upload_completed_at' => now(),
            'size' => 1024,
            'media_type' => MediaType::VIDEO,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'publish_to_youtube' => true,
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO,
            'youtube_status' => YouTubePublishStatus::QUEUED,
            'youtube_title' => 'Queued Video',
        ]);

        $user->media()->save($media);

        return $media;
    }
}
