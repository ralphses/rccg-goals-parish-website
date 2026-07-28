<?php

namespace App\Jobs;

use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\YouTubePublishStatus;
use App\Models\Media;
use App\Services\CloudinaryUploadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessVideoMediaUpload implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $mediaId)
    {
    }

    public function handle(CloudinaryUploadService $cloudinaryUploadService): void
    {
        $media = Media::find($this->mediaId);

        if (!$media || $media->media_type !== MediaType::VIDEO) {
            return;
        }

        if (blank($media->youtube_source_path) || !Storage::disk('local')->exists($media->youtube_source_path)) {
            $message = 'Stored source video could not be found. Re-upload the video file to continue.';

            $media->forceFill([
                'upload_status' => MediaUploadStatus::FAILED,
                'upload_last_error' => $message,
                'youtube_status' => $media->publish_to_youtube ? YouTubePublishStatus::FAILED : $media->youtube_status,
                'youtube_last_error' => $media->publish_to_youtube ? 'YouTube publishing could not start because the stored source video is missing.' : $media->youtube_last_error,
            ])->save();

            return;
        }

        $media->forceFill([
            'upload_status' => MediaUploadStatus::PROCESSING,
            'upload_last_error' => null,
            'upload_queued_at' => $media->upload_queued_at ?: now(),
        ])->save();

        try {
            $storedVideo = $cloudinaryUploadService->uploadStoredFile(
                $media->youtube_source_path,
                $media->file_name,
                'media',
                'video',
                'local'
            );

            $previousFilePath = $media->file_path;

            $media->forceFill([
                'file_path' => $storedVideo['url'],
                'size' => $storedVideo['size'] ?? $media->size,
                'upload_status' => MediaUploadStatus::READY,
                'upload_last_error' => null,
                'upload_completed_at' => now(),
            ])->save();

            if ($media->publish_to_youtube) {
                $media->forceFill([
                    'youtube_status' => YouTubePublishStatus::QUEUED,
                    'youtube_last_error' => null,
                    'youtube_publish_requested_at' => now(),
                    'youtube_published_at' => null,
                    'youtube_video_id' => null,
                    'youtube_video_url' => null,
                ])->save();

                PublishMediaToYouTube::dispatch($media->id);
            }

            if ($previousFilePath && $previousFilePath !== $storedVideo['url']) {
                $cloudinaryUploadService->deleteByUrl($previousFilePath, 'video');
            }
        } catch (Throwable $throwable) {
            $media->forceFill([
                'upload_status' => MediaUploadStatus::FAILED,
                'upload_last_error' => $throwable->getMessage(),
                'youtube_status' => $media->publish_to_youtube ? YouTubePublishStatus::FAILED : $media->youtube_status,
                'youtube_last_error' => $media->publish_to_youtube ? 'YouTube publishing could not start because the app video upload failed.' : $media->youtube_last_error,
            ])->save();

            throw $throwable;
        }
    }
}
