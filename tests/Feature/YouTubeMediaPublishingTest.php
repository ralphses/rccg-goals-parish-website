<?php

namespace Tests\Feature;

use App\Enums\MediaCategory;
use App\Enums\MediaType;
use App\Enums\MediaUploadStatus;
use App\Enums\UserRole;
use App\Enums\YouTubePublishStatus;
use App\Enums\YouTubeVideoFormat;
use App\Jobs\ProcessVideoMediaUpload;
use App\Jobs\PublishMediaToYouTube;
use App\Models\Media;
use App\Models\User;
use App\Models\YouTubeIntegration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class YouTubeMediaPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_upload_with_publish_intent_queues_job_and_stores_initial_status(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Queue::fake();
        $this->createYouTubeIntegration();

        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)->post(route('dashboard.media.store'), [
            'title' => 'Sunday Service Replay',
            'category' => MediaCategory::CHURCH_GALLERY->value,
            'media_type' => MediaType::VIDEO->value,
            'file' => UploadedFile::fake()->create('service.mp4', 1024, 'video/mp4'),
            'thumbnail_source_image' => UploadedFile::fake()->image('thumb-source.jpg', 2000, 1500),
            'cropped_thumbnail' => $this->asDataUrl(UploadedFile::fake()->image('thumb-cropped.jpg', 1600, 1200)),
            'publish_to_youtube' => '1',
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO->value,
            'youtube_title' => 'Sunday Service Replay',
            'youtube_description' => 'Fresh upload',
            'video_duration_seconds' => '120',
            'video_width' => '1080',
            'video_height' => '1920',
        ]);

        $response->assertRedirect(route('dashboard.media.index'));

        $media = Media::firstOrFail();
        $this->assertTrue($media->publish_to_youtube);
        $this->assertSame(MediaUploadStatus::QUEUED, $media->upload_status);
        $this->assertSame(YouTubePublishStatus::NOT_REQUESTED, $media->youtube_status);
        $this->assertSame(YouTubeVideoFormat::FULL_VIDEO, $media->youtube_format);
        $this->assertNotNull($media->youtube_source_path);
        $this->assertNull($media->file_path);
        Storage::disk('local')->assertExists($media->youtube_source_path);
        Queue::assertPushed(ProcessVideoMediaUpload::class);
        Queue::assertNotPushed(PublishMediaToYouTube::class);
    }

    public function test_non_video_media_cannot_request_youtube_publishing(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->createYouTubeIntegration();

        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->from(route('dashboard.media.create'))
            ->actingAs($user)
            ->post(route('dashboard.media.store'), [
                'title' => 'Image With YouTube',
                'category' => MediaCategory::CHURCH_GALLERY->value,
                'media_type' => MediaType::IMAGE->value,
                'source_image' => UploadedFile::fake()->image('source.jpg', 2000, 1500),
                'cropped_image' => $this->asDataUrl(UploadedFile::fake()->image('cropped.jpg', 1600, 1200)),
                'publish_to_youtube' => '1',
                'youtube_format' => YouTubeVideoFormat::FULL_VIDEO->value,
                'youtube_title' => 'Not allowed',
            ]);

        $response->assertRedirect(route('dashboard.media.create'));
        $response->assertSessionHasErrors(['publish_to_youtube']);
    }

    public function test_shorts_selection_fails_when_video_is_not_shorts_ready(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->createYouTubeIntegration();

        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->from(route('dashboard.media.create'))
            ->actingAs($user)
            ->post(route('dashboard.media.store'), [
                'title' => 'Landscape Clip',
                'category' => MediaCategory::CHURCH_GALLERY->value,
                'media_type' => MediaType::VIDEO->value,
                'file' => UploadedFile::fake()->create('landscape.mp4', 1024, 'video/mp4'),
                'thumbnail_source_image' => UploadedFile::fake()->image('thumb-source.jpg', 2000, 1500),
                'cropped_thumbnail' => $this->asDataUrl(UploadedFile::fake()->image('thumb-cropped.jpg', 1600, 1200)),
                'publish_to_youtube' => '1',
                'youtube_format' => YouTubeVideoFormat::SHORT->value,
                'youtube_title' => 'Landscape Clip',
                'video_duration_seconds' => '210',
                'video_width' => '1920',
                'video_height' => '1080',
            ]);

        $response->assertRedirect(route('dashboard.media.create'));
        $response->assertSessionHasErrors(['youtube_format']);
    }

    public function test_replacing_a_published_video_marks_youtube_state_queued_and_requeues(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Queue::fake();
        $this->createYouTubeIntegration();

        Storage::disk('public')->put('media/existing-video.mp4', 'video-binary');
        Storage::disk('public')->put('media/existing-thumb.jpg', 'thumb-binary');
        Storage::disk('local')->put('youtube-sources/2026/07/existing-video.mp4', 'private-source-binary');

        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $media = new Media([
            'title' => 'Existing Video',
            'file_name' => 'existing-video.mp4',
            'file_path' => url(Storage::url('media/existing-video.mp4')),
            'thumbnail_path' => url(Storage::url('media/existing-thumb.jpg')),
            'youtube_source_path' => 'youtube-sources/2026/07/existing-video.mp4',
            'size' => 1024,
            'media_type' => MediaType::VIDEO,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
            'upload_completed_at' => now(),
            'publish_to_youtube' => true,
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO,
            'youtube_status' => YouTubePublishStatus::UPLOADED_PRIVATE,
            'youtube_title' => 'Existing Video',
            'youtube_video_id' => 'abc123',
            'youtube_video_url' => 'https://www.youtube.com/watch?v=abc123',
        ]);
        $user->media()->save($media);

        $response = $this->actingAs($user)->put(route('dashboard.media.update', $media), [
            'title' => 'Existing Video',
            'category' => MediaCategory::CHURCH_GALLERY->value,
            'media_type' => MediaType::VIDEO->value,
            'file' => UploadedFile::fake()->create('replacement.mp4', 2048, 'video/mp4'),
            'thumbnail_source_image' => UploadedFile::fake()->image('thumb-source.jpg', 2000, 1500),
            'cropped_thumbnail' => $this->asDataUrl(UploadedFile::fake()->image('thumb-cropped.jpg', 1600, 1200)),
            'publish_to_youtube' => '1',
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO->value,
            'youtube_title' => 'Existing Video',
            'video_duration_seconds' => '160',
            'video_width' => '1080',
            'video_height' => '1920',
        ]);

        $response->assertRedirect(route('dashboard.media.index'));
        $media->refresh();

        $this->assertSame(MediaUploadStatus::QUEUED, $media->upload_status);
        $this->assertSame(YouTubePublishStatus::NOT_REQUESTED, $media->youtube_status);
        $this->assertNull($media->youtube_video_id);
        $this->assertNull($media->youtube_video_url);
        $this->assertNull($media->file_path);
        $this->assertNotSame('youtube-sources/2026/07/existing-video.mp4', $media->youtube_source_path);
        Storage::disk('local')->assertExists($media->youtube_source_path);
        Storage::disk('local')->assertMissing('youtube-sources/2026/07/existing-video.mp4');
        Queue::assertPushed(ProcessVideoMediaUpload::class);
        Queue::assertNotPushed(PublishMediaToYouTube::class);
    }

    public function test_dashboard_media_views_show_youtube_status_and_retry_actions(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEDIA]);

        $media = new Media([
            'title' => 'Retry Video',
            'file_name' => 'retry.mp4',
            'file_path' => 'https://example.test/retry.mp4',
            'thumbnail_path' => 'https://example.test/retry.jpg',
            'youtube_source_path' => 'youtube-sources/2026/07/retry.mp4',
            'size' => 1024,
            'media_type' => MediaType::VIDEO,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::FAILED,
            'upload_last_error' => 'Cloudinary timeout',
            'publish_to_youtube' => true,
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO,
            'youtube_status' => YouTubePublishStatus::FAILED,
            'youtube_title' => 'Retry Video',
            'youtube_video_url' => 'https://www.youtube.com/watch?v=retry123',
            'youtube_last_error' => 'Upload failed',
        ]);
        $user->media()->save($media);

        $indexResponse = $this->actingAs($user)->get(route('dashboard.media.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Retry App Upload');
        $indexResponse->assertDontSee('Retry YouTube Upload');
        $indexResponse->assertSee('Open');

        $showResponse = $this->actingAs($user)->get(route('dashboard.media.show', $media));
        $showResponse->assertOk();
        $showResponse->assertSee('App Upload Status');
        $showResponse->assertSee('Cloudinary timeout');
        $showResponse->assertSee('YouTube Status');
        $showResponse->assertSee('Upload failed');
        $showResponse->assertSee('Retry App Upload From Stored Source Copy');
        $showResponse->assertDontSee('Retry YouTube Upload From Stored Source Copy');
    }

    private function createYouTubeIntegration(): YouTubeIntegration
    {
        return YouTubeIntegration::create([
            'channel_id' => 'UC12345',
            'channel_title' => 'RCCG Goals Parish',
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
            'token_expires_at' => now()->addHour(),
        ]);
    }

    private function asDataUrl(UploadedFile $file): string
    {
        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($file->getRealPath()));
    }
}
