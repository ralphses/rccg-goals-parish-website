<?php

namespace Tests\Feature;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\UserRole;
use App\enums\YouTubePublishStatus;
use App\enums\YouTubeVideoFormat;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestMediaPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_videos_prefer_youtube_playback(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $media = new Media([
            'title' => 'Public Gallery Video',
            'file_name' => 'gallery-video.mp4',
            'file_path' => 'https://res.cloudinary.com/example/video/upload/media/gallery-video.mp4',
            'thumbnail_path' => 'https://res.cloudinary.com/example/image/upload/media/gallery-thumb.jpg',
            'size' => 1024,
            'media_type' => MediaType::VIDEO,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
            'upload_completed_at' => now(),
            'publish_to_youtube' => true,
            'youtube_format' => YouTubeVideoFormat::FULL_VIDEO,
            'youtube_status' => YouTubePublishStatus::UPLOADED_PRIVATE,
            'youtube_title' => 'Public Gallery Video',
            'youtube_video_url' => 'https://www.youtube.com/watch?v=abc123xyz89',
        ]);

        $user->media()->save($media);

        $response = $this->get(route('media'));

        $response->assertOk();
        $response->assertSee('video-popup', false);
        $response->assertSee('https://www.youtube.com/watch?v=abc123xyz89', false);
        $response->assertDontSee('https://res.cloudinary.com/example/video/upload/media/gallery-video.mp4', false);
        $response->assertDontSee('two-section__gallery-img-overly', false);
    }

    public function test_public_gallery_videos_fall_back_to_app_hosted_video_without_youtube_url(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $media = new Media([
            'title' => 'Fallback Gallery Video',
            'file_name' => 'fallback-video.mp4',
            'file_path' => 'https://res.cloudinary.com/example/video/upload/media/fallback-video.mp4',
            'thumbnail_path' => 'https://res.cloudinary.com/example/image/upload/media/fallback-thumb.jpg',
            'size' => 1024,
            'media_type' => MediaType::VIDEO,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
            'upload_completed_at' => now(),
            'publish_to_youtube' => false,
            'youtube_status' => YouTubePublishStatus::NOT_REQUESTED,
        ]);

        $user->media()->save($media);

        $response = $this->get(route('media'));

        $response->assertOk();
        $response->assertSee('https://res.cloudinary.com/example/video/upload/media/fallback-video.mp4', false);
    }
}
