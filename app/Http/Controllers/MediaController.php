<?php

namespace App\Http\Controllers;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\YouTubePublishStatus;
use App\enums\YouTubeVideoFormat;
use App\Jobs\ProcessVideoMediaUpload;
use App\Jobs\PublishMediaToYouTube;
use App\Models\Media;
use App\Models\YouTubeIntegration;
use App\Services\CloudinaryUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    private const IMAGE_MAX_KILOBYTES = 10240;

    private const MEDIA_FILE_MAX_KILOBYTES = 61440;

    public function __construct(private CloudinaryUploadService $cloudinaryUploadService)
    {
    }

    public function index(Request $request)
    {
        $query = Media::with('mediable');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->sort) {
            match ($request->sort) {
                'latest' => $query->latest(),
                'oldest' => $query->oldest(),
                'title' => $query->orderBy('title', 'asc'),
                'title_desc' => $query->orderBy('title', 'desc'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $media = $query->paginate(12);
        $categories = MediaCategory::cases();
        $queuedVideos = Media::where('media_type', MediaType::VIDEO->value)
            ->where('publish_to_youtube', true)
            ->whereIn('youtube_status', [YouTubePublishStatus::QUEUED->value, YouTubePublishStatus::UPLOADING->value])
            ->count();
        $backgroundUploads = Media::where('media_type', MediaType::VIDEO->value)
            ->whereIn('upload_status', [MediaUploadStatus::QUEUED->value, MediaUploadStatus::PROCESSING->value])
            ->count();

        return view('dashboard.media.index', [
            'media' => $media,
            'categories' => $categories,
            'youtubeConnected' => $this->youtubeConnected(),
            'queuedVideoCount' => $queuedVideos,
            'backgroundUploadCount' => $backgroundUploads,
        ]);
    }

    public function create()
    {
        return view('dashboard.media.create', [
            'categories' => MediaCategory::cases(),
            'youtubeConnected' => $this->youtubeConnected(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rulesForStore($request));
        $mediaType = MediaType::from($validated['media_type']);
        $publishToYouTube = $this->shouldPublishToYouTube($validated, $mediaType);

        $mediaAttributes = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'is_public' => (bool) $request->input('is_public', false),
            'media_type' => $mediaType,
            'upload_status' => MediaUploadStatus::READY,
            'upload_last_error' => null,
            'upload_queued_at' => null,
            'upload_completed_at' => now(),
            'publish_to_youtube' => $publishToYouTube,
            'youtube_format' => $publishToYouTube ? YouTubeVideoFormat::from($validated['youtube_format']) : null,
            'youtube_status' => YouTubePublishStatus::NOT_REQUESTED,
            'youtube_title' => $publishToYouTube ? ($validated['youtube_title'] ?: $validated['title']) : null,
            'youtube_description' => $publishToYouTube ? ($validated['youtube_description'] ?? null) : null,
            'youtube_video_id' => null,
            'youtube_video_url' => null,
            'youtube_last_error' => null,
            'youtube_publish_requested_at' => null,
            'youtube_published_at' => null,
        ];

        if ($mediaType === MediaType::IMAGE) {
            $croppedImage = $this->storeCroppedImage($validated['cropped_image'], 'image');

            $mediaAttributes = array_merge($mediaAttributes, [
                'file_name' => $croppedImage['original_name'],
                'file_path' => $croppedImage['url'],
                'size' => $croppedImage['size'],
                'thumbnail_path' => null,
            ]);
        } elseif ($mediaType === MediaType::VIDEO) {
            $youtubeSourcePath = $this->storeYouTubeSourceCopy($request->file('file'));
            $croppedThumbnail = $this->storeCroppedImage($validated['cropped_thumbnail'], 'video-thumbnail');

            $mediaAttributes = array_merge($mediaAttributes, [
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => null,
                'size' => $request->file('file')->getSize(),
                'thumbnail_path' => $croppedThumbnail['url'],
                'youtube_source_path' => $youtubeSourcePath,
                'upload_status' => MediaUploadStatus::QUEUED,
                'upload_queued_at' => now(),
                'upload_completed_at' => null,
            ]);
        } else {
            $storedAudio = $this->storeUploadedFile($request->file('file'));

            $mediaAttributes = array_merge($mediaAttributes, [
                'file_name' => $storedAudio['original_name'],
                'file_path' => $storedAudio['url'],
                'size' => $storedAudio['size'],
                'thumbnail_path' => null,
                'youtube_source_path' => null,
            ]);
        }

        $media = new Media($mediaAttributes);
        auth()->user()->media()->save($media);

        if ($mediaType === MediaType::VIDEO) {
            $this->dispatchVideoUploadProcessing($media);
        }

        $message = $mediaType === MediaType::VIDEO
            ? 'Video upload queued. The public video file will finish uploading in the background.'
            : 'Media uploaded successfully.';

        return redirect()->route('dashboard.media.index')->with('success', $message);
    }

    public function show(Media $media)
    {
        return view('dashboard.media.show', compact('media'));
    }

    public function edit(Media $media)
    {
        return view('dashboard.media.edit', [
            'media' => $media,
            'categories' => MediaCategory::cases(),
            'youtubeConnected' => $this->youtubeConnected(),
        ]);
    }

    public function update(Request $request, Media $media)
    {
        $validated = $request->validate($this->rulesForUpdate($request, $media));
        $selectedType = MediaType::from($validated['media_type']);
        $publishToYouTube = $this->shouldPublishToYouTube($validated, $selectedType);
        $videoFileChanged = false;
        $wasPublishingToYouTube = $media->publish_to_youtube;

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'is_public' => (bool) $request->input('is_public', false),
            'media_type' => $selectedType,
            'publish_to_youtube' => $publishToYouTube,
            'youtube_format' => $publishToYouTube ? YouTubeVideoFormat::from($validated['youtube_format']) : null,
            'youtube_title' => $publishToYouTube ? ($validated['youtube_title'] ?: $validated['title']) : null,
            'youtube_description' => $publishToYouTube ? ($validated['youtube_description'] ?? null) : null,
        ];

        if ($selectedType === MediaType::IMAGE) {
            if ($media->media_type !== MediaType::IMAGE || $request->hasFile('source_image')) {
                $this->deleteStoredAsset($media->file_path);
                $this->deleteStoredAsset($media->thumbnail_path);
                $this->deleteYouTubeSourceCopy($media->youtube_source_path);

                $croppedImage = $this->storeCroppedImage($validated['cropped_image'], 'image');

                $data = array_merge($data, [
                    'file_name' => $croppedImage['original_name'],
                    'file_path' => $croppedImage['url'],
                    'size' => $croppedImage['size'],
                    'thumbnail_path' => null,
                    'youtube_source_path' => null,
                    'upload_status' => MediaUploadStatus::READY,
                    'upload_last_error' => null,
                    'upload_queued_at' => null,
                    'upload_completed_at' => now(),
                ]);
            }

            $data = array_merge($data, $this->resetYouTubeFields());
        } elseif ($selectedType === MediaType::VIDEO) {
            if ($media->media_type !== MediaType::VIDEO) {
                $this->deleteStoredAsset($media->file_path);
                $this->deleteStoredAsset($media->thumbnail_path);
                $this->deleteYouTubeSourceCopy($media->youtube_source_path);
                $data['file_path'] = null;
                $videoFileChanged = true;
            }

            if ($media->media_type !== MediaType::VIDEO || $request->hasFile('file')) {
                $oldSourcePath = $media->youtube_source_path;

                $youtubeSourcePath = $this->storeYouTubeSourceCopy($request->file('file'));
                $data = array_merge($data, [
                    'file_name' => $request->file('file')->getClientOriginalName(),
                    'file_path' => null,
                    'size' => $request->file('file')->getSize(),
                    'youtube_source_path' => $youtubeSourcePath,
                    'upload_status' => MediaUploadStatus::QUEUED,
                    'upload_last_error' => null,
                    'upload_queued_at' => now(),
                    'upload_completed_at' => null,
                ]);
                $videoFileChanged = true;

                if ($oldSourcePath && $oldSourcePath !== $youtubeSourcePath) {
                    $this->deleteYouTubeSourceCopy($oldSourcePath);
                }
            }

            if (
                $media->media_type !== MediaType::VIDEO ||
                $request->hasFile('thumbnail_source_image') ||
                empty($media->thumbnail_path)
            ) {
                $this->deleteStoredAsset($media->thumbnail_path);

                $croppedThumbnail = $this->storeCroppedImage($validated['cropped_thumbnail'], 'video-thumbnail');
                $data['thumbnail_path'] = $croppedThumbnail['url'];
            }

            if ($publishToYouTube) {
                $status = $videoFileChanged || !$media->publish_to_youtube
                    ? YouTubePublishStatus::NOT_REQUESTED
                    : $media->youtube_status;

                $data = array_merge($data, [
                    'youtube_status' => $status,
                    'youtube_publish_requested_at' => $videoFileChanged || !$media->publish_to_youtube ? null : $media->youtube_publish_requested_at,
                    'youtube_published_at' => $videoFileChanged ? null : $media->youtube_published_at,
                    'youtube_video_id' => $videoFileChanged ? null : $media->youtube_video_id,
                    'youtube_video_url' => $videoFileChanged ? null : $media->youtube_video_url,
                    'youtube_last_error' => null,
                ]);
            } else {
                $data = array_merge($data, $this->resetYouTubeFields());
            }
        } else {
            if ($media->media_type !== MediaType::AUDIO || $request->hasFile('file')) {
                $this->deleteStoredAsset($media->file_path);
                $this->deleteStoredAsset($media->thumbnail_path);
                $this->deleteYouTubeSourceCopy($media->youtube_source_path);

                $storedAudio = $this->storeUploadedFile($request->file('file'));
                $data = array_merge($data, [
                    'file_name' => $storedAudio['original_name'],
                    'file_path' => $storedAudio['url'],
                    'size' => $storedAudio['size'],
                    'thumbnail_path' => null,
                    'youtube_source_path' => null,
                    'upload_status' => MediaUploadStatus::READY,
                    'upload_last_error' => null,
                    'upload_queued_at' => null,
                    'upload_completed_at' => now(),
                ]);
            }

            $data = array_merge($data, $this->resetYouTubeFields());
        }

        $media->update($data);

        if ($selectedType === MediaType::VIDEO && $videoFileChanged) {
            $this->dispatchVideoUploadProcessing($media->fresh());
        } elseif (
            $selectedType === MediaType::VIDEO
            && $publishToYouTube
            && $media->fresh()->upload_status === MediaUploadStatus::READY
            && (!$wasPublishingToYouTube || $media->youtube_status === YouTubePublishStatus::FAILED)
        ) {
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

        $message = $selectedType === MediaType::VIDEO && $videoFileChanged
            ? 'Video update saved. The new public video file is uploading in the background.'
            : 'Media updated successfully.';

        return redirect()->route('dashboard.media.index')->with('success', $message);
    }

    public function destroy(Media $media)
    {
        $this->deleteMediaRecord($media);

        return redirect()->route('dashboard.media.index')->with('success', 'Media deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:media,id'],
        ]);

        $items = Media::whereIn('id', $validated['selected_ids'])->get();

        foreach ($items as $media) {
            $this->deleteMediaRecord($media);
        }

        return redirect()->route('dashboard.media.index')->with('success', $items->count() . ' media item(s) deleted successfully.');
    }

    public function retryYouTubePublish(Media $media)
    {
        abort_unless($media->canRetryYouTubePublish(), 422, 'This media item cannot be retried.');

        if (!$this->youtubeConnected()) {
            return redirect()->route('dashboard.media.show', $media)->with('error', 'Connect the church YouTube channel before retrying.');
        }

        if (blank($media->youtube_source_path) || !Storage::disk('local')->exists($media->youtube_source_path)) {
            $media->forceFill([
                'youtube_status' => YouTubePublishStatus::FAILED,
                'youtube_last_error' => 'Stored YouTube source file could not be found. Re-upload the video file and retry.',
            ])->save();

            return redirect()->route('dashboard.media.show', $media)->with('error', 'Stored YouTube source file is missing. Re-upload the video file first.');
        }

        $media->forceFill([
            'youtube_status' => YouTubePublishStatus::QUEUED,
            'youtube_last_error' => null,
            'youtube_publish_requested_at' => now(),
            'youtube_published_at' => null,
            'youtube_video_id' => null,
            'youtube_video_url' => null,
        ])->save();

        PublishMediaToYouTube::dispatch($media->id);

        return redirect()->route('dashboard.media.show', $media)->with('success', 'YouTube publish retry queued.');
    }

    public function retryVideoUpload(Media $media)
    {
        abort_unless($media->canRetryUploadProcessing(), 422, 'This media item cannot be retried.');

        if (blank($media->youtube_source_path) || !Storage::disk('local')->exists($media->youtube_source_path)) {
            $media->forceFill([
                'upload_status' => MediaUploadStatus::FAILED,
                'upload_last_error' => 'Stored source video could not be found. Re-upload the video file to continue.',
            ])->save();

            return redirect()->route('dashboard.media.show', $media)->with('error', 'Stored source video is missing. Re-upload the video file first.');
        }

        $media->forceFill([
            'upload_status' => MediaUploadStatus::QUEUED,
            'upload_last_error' => null,
            'upload_queued_at' => now(),
            'upload_completed_at' => null,
        ])->save();

        if ($media->publish_to_youtube) {
            $media->forceFill([
                'youtube_status' => YouTubePublishStatus::NOT_REQUESTED,
                'youtube_last_error' => null,
                'youtube_publish_requested_at' => null,
                'youtube_published_at' => null,
                'youtube_video_id' => null,
                'youtube_video_url' => null,
            ])->save();
        }

        $this->dispatchVideoUploadProcessing($media);

        return redirect()->route('dashboard.media.show', $media)->with('success', 'Video upload retry queued.');
    }

    private function rulesForStore(Request $request): array
    {
        $categoryValues = implode(',', array_map(fn ($case) => $case->value, MediaCategory::cases()));

        $rules = [
            'title' => 'required|string|max:255',
            'category' => 'required|in:' . $categoryValues,
            'media_type' => ['required', Rule::in(array_map(fn ($case) => $case->value, MediaType::cases()))],
            'is_public' => 'nullable|boolean',
            'source_image' => 'required_if:media_type,image|nullable|image|mimes:jpeg,jpg,png,webp|max:' . self::IMAGE_MAX_KILOBYTES,
            'cropped_image' => 'required_if:media_type,image|nullable|string',
            'file' => 'nullable|file|max:' . self::MEDIA_FILE_MAX_KILOBYTES,
            'thumbnail_source_image' => 'required_if:media_type,video|nullable|image|mimes:jpeg,jpg,png,webp|max:' . self::IMAGE_MAX_KILOBYTES,
            'cropped_thumbnail' => 'required_if:media_type,video|nullable|string',
            'publish_to_youtube' => 'nullable|boolean',
            'youtube_format' => ['nullable', Rule::in(array_map(fn ($case) => $case->value, YouTubeVideoFormat::cases()))],
            'youtube_title' => 'nullable|string|max:100',
            'youtube_description' => 'nullable|string|max:5000',
            'video_duration_seconds' => 'nullable|numeric|min:0',
            'video_width' => 'nullable|integer|min:1',
            'video_height' => 'nullable|integer|min:1',
        ];

        $mediaType = $request->input('media_type');

        if ($mediaType === MediaType::VIDEO->value) {
            $rules['file'] = 'required|file|mimetypes:video/mp4,video/quicktime,video/ogg,video/webm|max:' . self::MEDIA_FILE_MAX_KILOBYTES;
        }

        if ($mediaType === MediaType::AUDIO->value) {
            $rules['file'] = 'required|file|mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/ogg|max:' . self::MEDIA_FILE_MAX_KILOBYTES;
        }

        $this->applyYouTubeValidation($rules, $request, true);

        return $rules;
    }

    private function rulesForUpdate(Request $request, Media $media): array
    {
        $rules = $this->rulesForStore($request);
        $selectedType = $request->input('media_type', $media->media_type->value);

        $rules['source_image'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:' . self::IMAGE_MAX_KILOBYTES;
        $rules['cropped_image'] = 'nullable|string';
        $rules['thumbnail_source_image'] = 'nullable|image|mimes:jpeg,jpg,png,webp|max:' . self::IMAGE_MAX_KILOBYTES;
        $rules['cropped_thumbnail'] = 'nullable|string';
        $rules['file'] = 'nullable|file|max:' . self::MEDIA_FILE_MAX_KILOBYTES;

        if ($selectedType === MediaType::IMAGE->value && ($media->media_type !== MediaType::IMAGE || $request->hasFile('source_image'))) {
            $rules['source_image'] = 'required|image|mimes:jpeg,jpg,png,webp|max:' . self::IMAGE_MAX_KILOBYTES;
            $rules['cropped_image'] = 'required|string';
        }

        if ($selectedType === MediaType::VIDEO->value) {
            $rules['file'] = ($media->media_type !== MediaType::VIDEO || $request->hasFile('file'))
                ? 'required|file|mimetypes:video/mp4,video/quicktime,video/ogg,video/webm|max:' . self::MEDIA_FILE_MAX_KILOBYTES
                : 'nullable|file|mimetypes:video/mp4,video/quicktime,video/ogg,video/webm|max:' . self::MEDIA_FILE_MAX_KILOBYTES;

            if ($media->media_type !== MediaType::VIDEO || $request->hasFile('thumbnail_source_image') || empty($media->thumbnail_path)) {
                $rules['thumbnail_source_image'] = 'required|image|mimes:jpeg,jpg,png,webp|max:' . self::IMAGE_MAX_KILOBYTES;
                $rules['cropped_thumbnail'] = 'required|string';
            }
        }

        if ($selectedType === MediaType::AUDIO->value) {
            $rules['file'] = ($media->media_type !== MediaType::AUDIO || $request->hasFile('file'))
                ? 'required|file|mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/ogg|max:' . self::MEDIA_FILE_MAX_KILOBYTES
                : 'nullable|file|mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/ogg|max:' . self::MEDIA_FILE_MAX_KILOBYTES;
        }

        if ($selectedType === MediaType::IMAGE->value) {
            $rules['file'] = 'nullable';
        }

        $this->applyYouTubeValidation($rules, $request, false, $media);

        return $rules;
    }

    private function applyYouTubeValidation(array &$rules, Request $request, bool $isStore, ?Media $media = null): void
    {
        $mediaType = $request->input('media_type', $media?->media_type?->value);
        $publishRequested = filter_var($request->input('publish_to_youtube', false), FILTER_VALIDATE_BOOLEAN);

        if ($publishRequested && $mediaType !== MediaType::VIDEO->value) {
            throw ValidationException::withMessages([
                'publish_to_youtube' => 'Only video uploads can be published to YouTube.',
            ]);
        }

        if ($publishRequested && !$this->youtubeConnected()) {
            throw ValidationException::withMessages([
                'publish_to_youtube' => 'Connect the church YouTube channel in settings before enabling YouTube publishing.',
            ]);
        }

        if (!$publishRequested) {
            return;
        }

        $rules['youtube_format'][] = 'required';
        $rules['youtube_title'] = 'required|string|max:100';

        if ($request->input('youtube_format') === YouTubeVideoFormat::SHORT->value) {
            $width = (int) $request->input('video_width');
            $height = (int) $request->input('video_height');
            $duration = (float) $request->input('video_duration_seconds');

            if ($width < 1 || $height < 1 || $duration <= 0) {
                throw ValidationException::withMessages([
                    'youtube_format' => 'Short uploads must include detected video dimensions and duration.',
                ]);
            }

            if ($duration > 180) {
                throw ValidationException::withMessages([
                    'youtube_format' => 'Short uploads must be 3 minutes or less.',
                ]);
            }

            if ($height < $width) {
                throw ValidationException::withMessages([
                    'youtube_format' => 'Short uploads must use a vertical or square video file.',
                ]);
            }
        }
    }

    private function storeUploadedFile($file): array
    {
        $resourceType = str_starts_with((string) $file->getMimeType(), 'video/')
            ? 'video'
            : (str_starts_with((string) $file->getMimeType(), 'audio/') ? 'raw' : 'image');

        return $this->cloudinaryUploadService->uploadFile($file, 'media', $resourceType);
    }

    private function storeCroppedImage(string $dataUrl, string $prefix): array
    {
        if (!preg_match('/^data:image\/(?P<type>[a-zA-Z0-9.+-]+);base64,(?P<data>.+)$/', $dataUrl)) {
            throw ValidationException::withMessages([
                $prefix === 'video-thumbnail' ? 'cropped_thumbnail' : 'cropped_image' => 'Invalid cropped image payload.',
            ]);
        }

        try {
            return $this->cloudinaryUploadService->uploadDataUrl($dataUrl, 'media', $prefix, 'image');
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                $prefix === 'video-thumbnail' ? 'cropped_thumbnail' : 'cropped_image' => 'Unable to process cropped image.',
            ]);
        }
    }

    private function deleteStoredAsset(?string $url): void
    {
        if (!empty($url)) {
            $resourceType = str_contains((string) $url, '/video/upload/')
                ? 'video'
                : (preg_match('/\.(mp3|wav|ogg)$/i', (string) $url) ? 'raw' : 'image');

            $this->cloudinaryUploadService->deleteByUrl($url, $resourceType);
        }
    }

    private function shouldPublishToYouTube(array $validated, MediaType $mediaType): bool
    {
        return $mediaType === MediaType::VIDEO
            && filter_var($validated['publish_to_youtube'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private function youtubeConnected(): bool
    {
        return YouTubeIntegration::query()->whereNotNull('refresh_token')->exists();
    }

    private function resetYouTubeFields(): array
    {
        return [
            'publish_to_youtube' => false,
            'youtube_format' => null,
            'youtube_status' => YouTubePublishStatus::NOT_REQUESTED,
            'youtube_title' => null,
            'youtube_description' => null,
            'youtube_video_id' => null,
            'youtube_video_url' => null,
            'youtube_last_error' => null,
            'youtube_publish_requested_at' => null,
            'youtube_published_at' => null,
        ];
    }

    private function dispatchVideoUploadProcessing(Media $media): void
    {
        ProcessVideoMediaUpload::dispatch($media->id)->onConnection('background');
    }

    private function storeYouTubeSourceCopy(UploadedFile $file): string
    {
        $directory = 'youtube-sources/' . now()->format('Y/m');
        $name = now()->timestamp . '_' . uniqid('video_', true) . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());

        return $file->storeAs($directory, $name, 'local');
    }

    private function deleteYouTubeSourceCopy(?string $path): void
    {
        if ($path) {
            Storage::disk('local')->delete($path);
        }
    }

    private function deleteMediaRecord(Media $media): void
    {
        $this->deleteStoredAsset($media->file_path);
        $this->deleteStoredAsset($media->thumbnail_path);
        $this->deleteYouTubeSourceCopy($media->youtube_source_path);
        $media->delete();
    }
}
