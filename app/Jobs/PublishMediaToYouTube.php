<?php

namespace App\Jobs;

use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\YouTubePublishStatus;
use App\Models\Media;
use App\Models\YouTubeIntegration;
use App\Services\YouTubeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class PublishMediaToYouTube implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $mediaId)
    {
    }

    public function handle(YouTubeService $youTubeService): void
    {
        $media = Media::find($this->mediaId);

        if (!$media || $media->media_type !== MediaType::VIDEO || !$media->publish_to_youtube) {
            return;
        }

        if ($media->upload_status !== MediaUploadStatus::READY) {
            $media->forceFill([
                'youtube_status' => YouTubePublishStatus::FAILED,
                'youtube_last_error' => 'App video upload is not ready yet. Wait for the background upload to finish, then retry YouTube publishing.',
            ])->save();

            return;
        }

        $integration = YouTubeIntegration::query()->first();

        if (!$integration || !$integration->hasValidToken()) {
            $media->forceFill([
                'youtube_status' => YouTubePublishStatus::FAILED,
                'youtube_last_error' => 'YouTube channel is not connected.',
            ])->save();

            return;
        }

        $media->forceFill([
            'youtube_status' => YouTubePublishStatus::UPLOADING,
            'youtube_last_error' => null,
            'youtube_publish_requested_at' => $media->youtube_publish_requested_at ?: now(),
        ])->save();

        try {
            $result = $youTubeService->uploadMedia($media, $integration);

            $media->forceFill([
                'youtube_status' => $result['status'],
                'youtube_video_id' => $result['video_id'],
                'youtube_video_url' => $result['video_url'],
                'youtube_last_error' => null,
                'youtube_published_at' => now(),
            ])->save();
        } catch (Throwable $throwable) {
            $media->forceFill([
                'youtube_status' => YouTubePublishStatus::FAILED,
                'youtube_last_error' => $throwable->getMessage(),
            ])->save();

            throw $throwable;
        }
    }
}
