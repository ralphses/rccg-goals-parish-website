<?php

namespace App\Http\Controllers;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\Models\Sermon;
use App\Models\Media;
use App\Models\User;
use App\enums\SermonStatus;
use App\Models\SermonAttachment;
use App\Services\CloudinaryUploadService;
use App\Services\CroppedImageUploadService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SermonController extends Controller
{
    public function __construct(
        private CloudinaryUploadService $cloudinaryUploadService,
        private CroppedImageUploadService $croppedImageUploadService
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sermon::query()->with('speaker');

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name') {
            $query->orderBy('title');
        } elseif ($sort === 'name_desc') {
            $query->orderByDesc('title');
        }

        $sermons = $query->paginate(10);

        return view('dashboard.sermons.index', compact('sermons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $speakers = User::where('role', 'pastor')->get();
        $statuses = SermonStatus::cases();
        $mediaLibrary = $this->sermonMediaLibrary();

        return view('dashboard.sermons.create', compact('speakers', 'statuses', 'mediaLibrary'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to create a sermon.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'sermon_date' => 'required|date',
            'duration' => 'nullable|string',
            'speaker_id' => 'required|exists:users,id',
            'cover_image_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'cover_image_cropped' => 'nullable|string',
            'cover_media_id' => ['nullable', $this->sermonMediaRule(MediaType::IMAGE)],
            'audio_media_id' => ['nullable', $this->sermonMediaRule(MediaType::AUDIO)],
            'audio_url' => 'nullable|url',
            'video_media_id' => ['nullable', $this->sermonMediaRule(MediaType::VIDEO)],
            'video_url' => 'nullable|url',
            'status' => 'required|in:' . implode(',', SermonStatus::values()),
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($request->title);

        $validated = $this->applySermonMediaSelections($request, $validated);

        $sermon = Sermon::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $uploaded = $this->cloudinaryUploadService
                    ->uploadFile($file, 'sermon_attachments', 'raw');

                $sermon->attachments()->create([
                    'file_path' => $uploaded['url'],
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return redirect()->route('dashboard.sermons.index')->with('success', 'Sermon created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sermon $sermon)
    {
        return view('dashboard.sermons.show', compact('sermon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sermon $sermon)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to edit this sermon.');
        }
        $speakers = User::where('role', 'pastor')->get();
        $statuses = SermonStatus::cases();
        $mediaLibrary = $this->sermonMediaLibrary();

        return view('dashboard.sermons.edit', compact('sermon', 'speakers', 'statuses', 'mediaLibrary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sermon $sermon)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to update this sermon.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'sermon_date' => 'required|date',
            'duration' => 'nullable|string',
            'speaker_id' => 'required|exists:users,id',
            'cover_image_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'cover_image_cropped' => 'nullable|string',
            'cover_media_id' => ['nullable', $this->sermonMediaRule(MediaType::IMAGE)],
            'audio_media_id' => ['nullable', $this->sermonMediaRule(MediaType::AUDIO)],
            'audio_url' => 'nullable|url',
            'video_media_id' => ['nullable', $this->sermonMediaRule(MediaType::VIDEO)],
            'video_url' => 'nullable|url',
            'status' => 'required|in:' . implode(',', SermonStatus::values()),
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
        ]);


        $validated['slug'] = $this->generateUniqueSlug($request->title, $sermon->id);

        $validated = $this->applySermonMediaSelections($request, $validated, $sermon);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $uploaded = $this->cloudinaryUploadService
                    ->uploadFile($file, 'sermon_attachments', 'raw');

                $sermon->attachments()->create([
                    'file_path' => $uploaded['url'],
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        $sermon->update($validated);

        return redirect()->route('dashboard.sermons.index')->with('success', 'Sermon updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sermon $sermon)
    {
        $this->deleteSermonRecord($sermon);

        return redirect()->route('dashboard.sermons.index')->with('success', 'Sermon deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:sermons,id'],
        ]);

        $sermons = Sermon::with('attachments')->whereIn('id', $validated['selected_ids'])->get();

        foreach ($sermons as $sermon) {
            $this->deleteSermonRecord($sermon);
        }

        return redirect()->route('dashboard.sermons.index')->with('success', $sermons->count() . ' sermon(s) deleted successfully.');
    }

    /**
     * Download a sermon attachment.
     */
    public function downloadAttachment(SermonAttachment $attachment)
    {
        return redirect()->away($attachment->file_url ?? $attachment->file_path);
    }

    /**
     * Generate a unique slug.
     */
    private function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Sermon::where('slug', $slug)->when($excludeId, function ($query) use ($excludeId) {
            return $query->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    private function deleteSermonRecord(Sermon $sermon): void
    {
        if ($sermon->cover_image && !$sermon->cover_media_id) {
            $this->cloudinaryUploadService->deleteByUrl($sermon->cover_image, 'image');
        }

        foreach ($sermon->attachments as $attachment) {
            $this->cloudinaryUploadService->deleteByUrl($attachment->file_path, 'raw');
        }

        $sermon->delete();
    }

    private function sermonMediaLibrary(): array
    {
        $media = Media::query()
            ->where('category', MediaCategory::SERMON)
            ->where('upload_status', MediaUploadStatus::READY)
            ->orderByDesc('created_at')
            ->get();

        return [
            'images' => $media->where('media_type', MediaType::IMAGE),
            'videos' => $media->where('media_type', MediaType::VIDEO),
            'audios' => $media->where('media_type', MediaType::AUDIO),
        ];
    }

    private function sermonMediaRule(MediaType $type)
    {
        return Rule::exists('media', 'id')->where(function ($query) use ($type) {
            $query
                ->where('category', MediaCategory::SERMON->value)
                ->where('media_type', $type->value)
                ->where('upload_status', MediaUploadStatus::READY->value);
        });
    }

    private function applySermonMediaSelections(Request $request, array $validated, ?Sermon $sermon = null): array
    {
        $coverMedia = !empty($validated['cover_media_id']) ? Media::find($validated['cover_media_id']) : null;
        $audioMedia = !empty($validated['audio_media_id']) ? Media::find($validated['audio_media_id']) : null;
        $videoMedia = !empty($validated['video_media_id']) ? Media::find($validated['video_media_id']) : null;

        if ($request->hasFile('cover_image_source')) {
            $request->validate([
                'cover_image_cropped' => ['required', 'string'],
            ]);

            if ($sermon && $sermon->cover_image && !$sermon->cover_media_id) {
                $this->cloudinaryUploadService->deleteByUrl($sermon->cover_image, 'image');
            }

            $validated['cover_image'] = $this->croppedImageUploadService
                ->storeFromDataUrl($request->string('cover_image_cropped')->toString(), 'sermons', 'sermon-cover', 'cover_image_cropped')['url'];
            $validated['cover_media_id'] = null;
        } elseif ($coverMedia) {
            if ($sermon && $sermon->cover_image && !$sermon->cover_media_id && $sermon->cover_image !== $coverMedia->file_path) {
                $this->cloudinaryUploadService->deleteByUrl($sermon->cover_image, 'image');
            }

            $validated['cover_image'] = $coverMedia->file_path;
            $validated['cover_media_id'] = $coverMedia->id;
        } elseif ($sermon && $sermon->cover_image) {
            $validated['cover_image'] = $sermon->cover_image;
            $validated['cover_media_id'] = $sermon->cover_media_id;
        }

        if ($audioMedia) {
            $validated['audio_url'] = $audioMedia->file_url;
            $validated['audio_media_id'] = $audioMedia->id;
        }

        if ($videoMedia) {
            $validated['video_url'] = $videoMedia->youtube_video_url ?: $videoMedia->public_video_url;
            $validated['video_media_id'] = $videoMedia->id;
        }

        unset($validated['cover_image_source'], $validated['cover_image_cropped']);

        return $validated;
    }
}
