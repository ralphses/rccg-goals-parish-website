<?php

namespace Tests\Feature;

use App\Enums\MediaCategory;
use App\Enums\MediaType;
use App\Enums\MediaUploadStatus;
use App\Enums\UserRole;
use App\Jobs\ProcessVideoMediaUpload;
use App\Models\Media;
use App\Models\User;
use App\Support\MediaUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_upload_requires_cropped_output_and_stores_the_cropped_result(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $sourceImage = UploadedFile::fake()->image('source.jpg', 2000, 1200);
        $croppedImage = UploadedFile::fake()->image('cropped.jpg', 1600, 1200);

        $response = $this->actingAs($user)->post(route('dashboard.media.store'), [
            'title' => 'Sunday Service Banner',
            'category' => MediaCategory::CHURCH_GALLERY->value,
            'media_type' => MediaType::IMAGE->value,
            'source_image' => $sourceImage,
            'cropped_image' => $this->asDataUrl($croppedImage, 'image/jpeg'),
            'is_public' => '1',
        ]);

        $response->assertRedirect(route('dashboard.media.index'));

        $media = Media::first();

        $this->assertNotNull($media);
        $this->assertSame(MediaType::IMAGE, $media->media_type);
        $this->assertNull($media->thumbnail_path);
        $this->assertTrue(MediaUrl::isAbsolute($media->file_path));
        Storage::disk('public')->assertExists(MediaUrl::toStoragePath($media->file_path));
    }

    public function test_video_upload_requires_video_file_and_cropped_thumbnail(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Queue::fake();

        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $videoFile = UploadedFile::fake()->create('service.mp4', 1024, 'video/mp4');
        $thumbnailSource = UploadedFile::fake()->image('thumb-source.jpg', 2000, 1200);
        $croppedThumbnail = UploadedFile::fake()->image('thumb-cropped.jpg', 1600, 1200);

        $response = $this->actingAs($user)->post(route('dashboard.media.store'), [
            'title' => 'Midweek Recharge',
            'category' => MediaCategory::CHURCH_GALLERY->value,
            'media_type' => MediaType::VIDEO->value,
            'file' => $videoFile,
            'thumbnail_source_image' => $thumbnailSource,
            'cropped_thumbnail' => $this->asDataUrl($croppedThumbnail, 'image/jpeg'),
            'is_public' => '1',
        ]);

        $response->assertRedirect(route('dashboard.media.index'));

        $media = Media::first();

        $this->assertNotNull($media);
        $this->assertSame(MediaType::VIDEO, $media->media_type);
        $this->assertNotNull($media->thumbnail_path);
        $this->assertNull($media->file_path);
        $this->assertSame(MediaUploadStatus::QUEUED, $media->upload_status);
        $this->assertTrue(MediaUrl::isAbsolute($media->thumbnail_path));
        Storage::disk('public')->assertExists(MediaUrl::toStoragePath($media->thumbnail_path));
        Storage::disk('local')->assertExists($media->youtube_source_path);
        Queue::assertPushed(ProcessVideoMediaUpload::class);
    }

    public function test_audio_upload_succeeds_without_crop_data(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $audioFile = UploadedFile::fake()->create('sermon.mp3', 512, 'audio/mpeg');

        $response = $this->actingAs($user)->post(route('dashboard.media.store'), [
            'title' => 'Prayer Charge',
            'category' => MediaCategory::TESTIMONY->value,
            'media_type' => MediaType::AUDIO->value,
            'file' => $audioFile,
        ]);

        $response->assertRedirect(route('dashboard.media.index'));

        $media = Media::first();

        $this->assertNotNull($media);
        $this->assertSame(MediaType::AUDIO, $media->media_type);
        $this->assertNull($media->thumbnail_path);
        $this->assertTrue(MediaUrl::isAbsolute($media->file_path));
        Storage::disk('public')->assertExists(MediaUrl::toStoragePath($media->file_path));
    }

    public function test_invalid_type_and_crop_combinations_fail_validation(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->from(route('dashboard.media.create'))
            ->actingAs($user)
            ->post(route('dashboard.media.store'), [
                'title' => 'Incomplete Image',
                'category' => MediaCategory::CHURCH_GALLERY->value,
                'media_type' => MediaType::IMAGE->value,
                'source_image' => UploadedFile::fake()->image('source.jpg', 1200, 800),
            ]);

        $response->assertRedirect(route('dashboard.media.create'));
        $response->assertSessionHasErrors(['cropped_image']);
    }

    public function test_replacing_an_image_requires_a_new_cropped_output(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $media = $this->createMediaForUser($user, [
            'media_type' => MediaType::IMAGE,
            'file_path' => url(Storage::url('media/existing-image.jpg')),
            'thumbnail_path' => null,
        ]);

        Storage::disk('public')->put('media/existing-image.jpg', 'existing-image');

        $response = $this->from(route('dashboard.media.edit', $media))
            ->actingAs($user)
            ->put(route('dashboard.media.update', $media), [
                'title' => 'Updated Image',
                'category' => MediaCategory::CHURCH_GALLERY->value,
                'media_type' => MediaType::IMAGE->value,
                'source_image' => UploadedFile::fake()->image('replacement.jpg', 2000, 1200),
                'is_public' => '1',
            ]);

        $response->assertRedirect(route('dashboard.media.edit', $media));
        $response->assertSessionHasErrors(['cropped_image']);
    }

    public function test_video_thumbnail_can_be_updated_without_replacing_the_video_file(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        Storage::disk('public')->put('media/existing-video.mp4', 'video-binary');
        Storage::disk('public')->put('media/existing-thumb.jpg', 'thumb-binary');

        $media = $this->createMediaForUser($user, [
            'media_type' => MediaType::VIDEO,
            'file_path' => url(Storage::url('media/existing-video.mp4')),
            'thumbnail_path' => url(Storage::url('media/existing-thumb.jpg')),
            'upload_status' => MediaUploadStatus::READY,
        ]);

        $newThumbnailSource = UploadedFile::fake()->image('new-thumb-source.jpg', 2000, 1200);
        $newCroppedThumbnail = UploadedFile::fake()->image('new-thumb-cropped.jpg', 1600, 1200);

        $response = $this->actingAs($user)->put(route('dashboard.media.update', $media), [
            'title' => 'Updated Video',
            'category' => MediaCategory::CHURCH_GALLERY->value,
            'media_type' => MediaType::VIDEO->value,
            'thumbnail_source_image' => $newThumbnailSource,
            'cropped_thumbnail' => $this->asDataUrl($newCroppedThumbnail, 'image/jpeg'),
            'is_public' => '1',
        ]);

        $response->assertRedirect(route('dashboard.media.index'));

        $media->refresh();

        $this->assertSame(url(Storage::url('media/existing-video.mp4')), $media->file_path);
        $this->assertNotSame(url(Storage::url('media/existing-thumb.jpg')), $media->thumbnail_path);
        $this->assertTrue(MediaUrl::isAbsolute($media->thumbnail_path));
        Storage::disk('public')->assertExists(MediaUrl::toStoragePath($media->thumbnail_path));
    }

    private function createMediaForUser(User $user, array $overrides): Media
    {
        $media = new Media([
            'title' => $overrides['title'] ?? 'Existing Media',
            'file_name' => basename($overrides['file_path']),
            'file_path' => $overrides['file_path'],
            'thumbnail_path' => $overrides['thumbnail_path'] ?? null,
            'youtube_source_path' => $overrides['youtube_source_path'] ?? null,
            'upload_status' => $overrides['upload_status'] ?? MediaUploadStatus::READY,
            'upload_last_error' => $overrides['upload_last_error'] ?? null,
            'upload_queued_at' => $overrides['upload_queued_at'] ?? null,
            'upload_completed_at' => $overrides['upload_completed_at'] ?? now(),
            'size' => $overrides['size'] ?? 1024,
            'media_type' => $overrides['media_type'],
            'category' => $overrides['category'] ?? MediaCategory::CHURCH_GALLERY,
            'is_public' => $overrides['is_public'] ?? true,
        ]);

        $user->media()->save($media);

        return $media;
    }

    private function asDataUrl(UploadedFile $file, string $mimeType): string
    {
        return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
    }
}
