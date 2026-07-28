<?php

namespace App\Services;

use App\enums\YouTubePublishStatus;
use App\Models\Media;
use App\Models\YouTubeIntegration;
use App\Support\MediaUrl;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class YouTubeService
{
    private const AUTH_BASE_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const YOUTUBE_API_BASE = 'https://www.googleapis.com/youtube/v3';
    private const YOUTUBE_UPLOAD_BASE = 'https://www.googleapis.com/upload/youtube/v3';

    public function authorizationUrl(string $state): string
    {
        return self::AUTH_BASE_URL . '?' . http_build_query([
            'client_id' => $this->clientId(),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'scope' => implode(' ', [
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/youtube.readonly',
            ]),
            'state' => $state,
        ]);
    }

    public function exchangeCodeForTokens(string $code): array
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect_uri' => $this->redirectUri(),
            'grant_type' => 'authorization_code',
        ]);

        return $this->mapTokenResponse($response);
    }

    public function refreshAccessToken(YouTubeIntegration $integration): string
    {
        if (filled($integration->token_expires_at) && $integration->token_expires_at->isFuture() && filled($integration->access_token)) {
            return (string) $integration->access_token;
        }

        if (blank($integration->refresh_token)) {
            throw new RuntimeException('YouTube refresh token is missing.');
        }

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'refresh_token' => $integration->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $payload = $this->mapTokenResponse($response, (string) $integration->refresh_token);

        $integration->forceFill([
            'access_token' => $payload['access_token'],
            'refresh_token' => $payload['refresh_token'],
            'token_expires_at' => now()->addSeconds($payload['expires_in']),
            'last_used_at' => now(),
            'last_error' => null,
        ])->save();

        return $payload['access_token'];
    }

    public function fetchChannel(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get(self::YOUTUBE_API_BASE . '/channels', [
                'part' => 'snippet',
                'mine' => 'true',
            ]);

        $response->throw();

        $item = data_get($response->json(), 'items.0');

        if (!$item) {
            throw new RuntimeException('Unable to determine the connected YouTube channel.');
        }

        return [
            'channel_id' => data_get($item, 'id'),
            'channel_title' => data_get($item, 'snippet.title'),
            'channel_thumbnail_url' => data_get($item, 'snippet.thumbnails.default.url'),
        ];
    }

    public function uploadMedia(Media $media, YouTubeIntegration $integration): array
    {
        $accessToken = $this->refreshAccessToken($integration);
        $source = $this->sourcePayload($media);

        $sessionStart = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Upload-Content-Length' => (string) strlen($source['contents']),
                'X-Upload-Content-Type' => $source['mime_type'],
            ])
            ->withBody(json_encode([
                'snippet' => [
                    'title' => $media->youtube_title ?: $media->title,
                    'description' => $media->youtube_description ?: '',
                ],
                'status' => [
                    'privacyStatus' => 'private',
                ],
            ], JSON_THROW_ON_ERROR), 'application/json; charset=UTF-8')
            ->send('POST', self::YOUTUBE_UPLOAD_BASE . '/videos?uploadType=resumable&part=snippet,status');

        $sessionStart->throw();

        $uploadUrl = $sessionStart->header('Location');

        if (!$uploadUrl) {
            throw new RuntimeException('YouTube did not return an upload session URL.');
        }

        $uploadResponse = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => $source['mime_type'],
            ])
            ->withBody($source['contents'], $source['mime_type'])
            ->send('PUT', $uploadUrl);

        $uploadResponse->throw();

        $videoId = (string) $uploadResponse->json('id');

        if ($videoId === '') {
            throw new RuntimeException('YouTube did not return a video identifier.');
        }

        if ($media->thumbnail_path) {
            $this->uploadThumbnail($accessToken, $videoId, $media->thumbnail_path);
        }

        $integration->forceFill([
            'last_used_at' => now(),
            'last_error' => null,
        ])->save();

        return [
            'status' => YouTubePublishStatus::UPLOADED_PRIVATE,
            'video_id' => $videoId,
            'video_url' => 'https://www.youtube.com/watch?v=' . $videoId,
        ];
    }

    public function uploadThumbnail(string $accessToken, string $videoId, string $thumbnailUrl): void
    {
        $contents = $this->fetchBinaryFromUrl($thumbnailUrl);

        Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'image/jpeg',
            ])
            ->withBody($contents, 'image/jpeg')
            ->send('POST', self::YOUTUBE_UPLOAD_BASE . '/thumbnails/set?videoId=' . urlencode($videoId))
            ->throw();
    }

    private function sourcePayload(Media $media): array
    {
        if (blank($media->youtube_source_path)) {
            throw new RuntimeException('YouTube source copy is missing for this video. Re-upload the video file and try again.');
        }

        if (!Storage::disk('local')->exists($media->youtube_source_path)) {
            throw new RuntimeException('Stored YouTube source file could not be found. Re-upload the video file and retry.');
        }

        $contents = Storage::disk('local')->get($media->youtube_source_path);

        return [
            'contents' => $contents,
            'mime_type' => $this->mimeTypeForFileName($media->file_name),
        ];
    }

    private function fetchBinaryFromUrl(string $url): string
    {
        $storagePath = MediaUrl::toStoragePath($url);

        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->get($storagePath);
        }

        $response = Http::timeout(120)->get($url);
        $response->throw();

        return $response->body();
    }

    private function mapTokenResponse(Response $response, ?string $fallbackRefreshToken = null): array
    {
        $response->throw();

        $json = $response->json();

        return [
            'access_token' => (string) data_get($json, 'access_token'),
            'refresh_token' => (string) (data_get($json, 'refresh_token') ?: $fallbackRefreshToken),
            'expires_in' => (int) data_get($json, 'expires_in', 3600),
        ];
    }

    private function mimeTypeForFileName(string $fileName): string
    {
        return match (Str::lower(pathinfo($fileName, PATHINFO_EXTENSION))) {
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'ogg' => 'video/ogg',
            'webm' => 'video/webm',
            default => 'application/octet-stream',
        };
    }

    private function clientId(): string
    {
        return (string) config('services.youtube.client_id');
    }

    private function clientSecret(): string
    {
        return (string) config('services.youtube.client_secret');
    }

    private function redirectUri(): string
    {
        return (string) (config('services.youtube.redirect') ?: route('settings.youtube.callback'));
    }
}
