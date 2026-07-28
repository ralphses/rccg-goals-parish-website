<?php

namespace App\Services;

use App\Support\MediaUrl;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryUploadService
{
    public function uploadFile(UploadedFile $file, string $folder, string $resourceType = 'image'): array
    {
        if ($this->shouldUseLocalFallback()) {
            return $this->uploadLocally($file, $folder);
        }

        $timestamp = time();
        $params = [
            'folder' => trim($folder, '/'),
            'timestamp' => $timestamp,
            'use_filename' => 'true',
            'unique_filename' => 'true',
        ];

        $response = Http::asMultipart()
            ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
            ->post($this->uploadEndpoint($resourceType), array_merge($params, [
                'api_key' => $this->apiKey(),
                'signature' => $this->signature($params),
            ]));

        return $this->mapUploadResponse($response, $file->getClientOriginalName(), $file->getSize());
    }

    public function uploadDataUrl(string $dataUrl, string $folder, string $prefix, string $resourceType = 'image'): array
    {
        if ($this->shouldUseLocalFallback()) {
            return $this->uploadDataUrlLocally($dataUrl, $folder, $prefix);
        }

        $timestamp = time();
        $params = [
            'folder' => trim($folder, '/'),
            'public_id' => trim($folder, '/') . '/' . Str::slug($prefix) . '-' . Str::random(10),
            'timestamp' => $timestamp,
        ];

        $response = Http::asForm()->post($this->uploadEndpoint($resourceType), array_merge($params, [
            'file' => $dataUrl,
            'api_key' => $this->apiKey(),
            'signature' => $this->signature($params),
        ]));

        return $this->mapUploadResponse($response, basename($params['public_id']) . '.jpg');
    }

    public function uploadStoredFile(
        string $path,
        string $originalName,
        string $folder,
        string $resourceType = 'image',
        string $disk = 'local'
    ): array {
        if (!Storage::disk($disk)->exists($path)) {
            throw new RuntimeException('Stored upload source could not be found.');
        }

        if ($this->shouldUseLocalFallback()) {
            return $this->uploadStoredFileLocally($path, $originalName, $folder, $disk);
        }

        $absolutePath = Storage::disk($disk)->path($path);
        $timestamp = time();
        $params = [
            'folder' => trim($folder, '/'),
            'timestamp' => $timestamp,
            'use_filename' => 'true',
            'unique_filename' => 'true',
        ];

        $response = Http::asMultipart()
            ->attach('file', fopen($absolutePath, 'r'), $originalName)
            ->post($this->uploadEndpoint($resourceType), array_merge($params, [
                'api_key' => $this->apiKey(),
                'signature' => $this->signature($params),
            ]));

        return $this->mapUploadResponse(
            $response,
            $originalName,
            Storage::disk($disk)->size($path)
        );
    }

    public function deleteByUrl(?string $url, string $resourceType = 'image'): void
    {
        if (empty($url)) {
            return;
        }

        if ($this->shouldUseLocalFallback()) {
            $storagePath = MediaUrl::toStoragePath($url);
            if ($storagePath) {
                Storage::disk('public')->delete($storagePath);
            }

            return;
        }

        if (!MediaUrl::isAbsolute($url) || !str_contains($url, 'res.cloudinary.com')) {
            return;
        }

        $publicId = $this->extractPublicIdFromUrl($url);

        if (!$publicId) {
            return;
        }

        $timestamp = time();
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ];

        Http::asForm()->post($this->destroyEndpoint($resourceType), array_merge($params, [
            'api_key' => $this->apiKey(),
            'signature' => $this->signature($params),
        ]))->throw();
    }

    public function isConfigured(): bool
    {
        return filled(config('cloudinary.cloud_name'))
            && filled(config('cloudinary.api_key'))
            && filled(config('cloudinary.api_secret'));
    }

    private function shouldUseLocalFallback(): bool
    {
        return app()->runningUnitTests() || !$this->isConfigured();
    }

    private function uploadLocally(UploadedFile $file, string $folder): array
    {
        $fileName = now()->timestamp . '_' . Str::random(10) . '_' . $file->getClientOriginalName();
        $path = $file->storeAs(trim($folder, '/'), $fileName, 'public');

        return [
            'url' => url(Storage::url($path)),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    private function uploadDataUrlLocally(string $dataUrl, string $folder, string $prefix): array
    {
        if (!preg_match('/^data:image\/(?P<type>[a-zA-Z0-9.+-]+);base64,(?P<data>.+)$/', $dataUrl, $matches)) {
            throw new RuntimeException('Invalid data URL payload.');
        }

        $binary = base64_decode($matches['data'], true);

        if ($binary === false) {
            throw new RuntimeException('Invalid base64 payload.');
        }

        $fileName = now()->timestamp . '_' . Str::slug($prefix) . '_' . Str::random(10) . '.jpg';
        $path = trim($folder, '/') . '/' . $fileName;

        Storage::disk('public')->put($path, $binary);

        return [
            'url' => url(Storage::url($path)),
            'size' => Storage::disk('public')->size($path),
            'original_name' => $fileName,
        ];
    }

    private function uploadStoredFileLocally(string $path, string $originalName, string $folder, string $disk): array
    {
        $fileName = now()->timestamp . '_' . Str::random(10) . '_' . $originalName;
        $targetPath = trim($folder, '/') . '/' . $fileName;

        Storage::disk('public')->put($targetPath, Storage::disk($disk)->get($path));

        return [
            'url' => url(Storage::url($targetPath)),
            'size' => Storage::disk('public')->size($targetPath),
            'original_name' => $originalName,
        ];
    }

    private function mapUploadResponse(Response $response, string $originalName, ?int $fallbackSize = null): array
    {
        $response->throw();

        return [
            'url' => $response->json('secure_url'),
            'size' => $response->json('bytes') ?? $fallbackSize,
            'original_name' => $originalName,
        ];
    }

    private function uploadEndpoint(string $resourceType): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/upload',
            $this->cloudName(),
            $resourceType
        );
    }

    private function destroyEndpoint(string $resourceType): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/destroy',
            $this->cloudName(),
            $resourceType
        );
    }

    private function extractPublicIdFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (!$path) {
            return null;
        }

        $uploadMarker = '/upload/';
        $uploadPosition = strpos($path, $uploadMarker);

        if ($uploadPosition === false) {
            return null;
        }

        $publicPath = substr($path, $uploadPosition + strlen($uploadMarker));
        $segments = array_values(array_filter(explode('/', $publicPath)));

        while ($segments && !preg_match('/^v\d+$/', $segments[0])) {
            array_shift($segments);
        }

        if ($segments && preg_match('/^v\d+$/', $segments[0])) {
            array_shift($segments);
        }

        if (empty($segments)) {
            return null;
        }

        $lastSegment = array_pop($segments);
        $lastSegment = preg_replace('/\.[^.]+$/', '', $lastSegment);
        $segments[] = $lastSegment;

        return implode('/', $segments);
    }

    private function signature(array $params): string
    {
        ksort($params);

        $signatureBase = collect($params)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value, $key) => $key . '=' . $value)
            ->implode('&');

        return sha1($signatureBase . $this->apiSecret());
    }

    private function cloudName(): string
    {
        return (string) config('cloudinary.cloud_name');
    }

    private function apiKey(): string
    {
        return (string) config('cloudinary.api_key');
    }

    private function apiSecret(): string
    {
        return (string) config('cloudinary.api_secret');
    }
}
