<?php

namespace App\Http\Controllers;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\YouTubePublishStatus;
use App\Models\Department;
use App\enums\EventStatus;
use App\Models\Event;
use App\Models\Media;
use App\Services\CloudinaryUploadService;
use App\Services\CroppedImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function __construct(
        private CloudinaryUploadService $cloudinaryUploadService,
        private CroppedImageUploadService $croppedImageUploadService
    )
    {
    }
    public function index(Request $request)
    {
        $query = Event::query();

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

        $events = $query->paginate(10);

        return view('dashboard.events.index', compact('events'));
    }

    public function create()
    {
        $departments = Department::all();
        $statuses = EventStatus::cases();
        $mediaLibrary = $this->eventMediaLibrary();

        return view('dashboard.events.create', compact('departments', 'statuses', 'mediaLibrary'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to create an event.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'image_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_cropped' => 'nullable|string',
            'image_media_id' => ['nullable', $this->eventMediaRule(MediaType::IMAGE)],
            'status' => 'required|in:' . implode(',', array_map(fn ($case) => $case->value, EventStatus::cases())),
            'department_id' => 'nullable|exists:departments,id',
            'video_link' => 'nullable|url',
            'video_media_id' => ['nullable', $this->eventMediaRule(MediaType::VIDEO)],
            'description_heading' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $validated = $this->applyEventMediaSelections($request, $validated);

        Event::create($validated);

        return redirect()->route('dashboard.events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        return view('dashboard.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to edit this event.');
        }
        $departments = Department::all();
        $statuses = EventStatus::cases();
        $mediaLibrary = $this->eventMediaLibrary();

        return view('dashboard.events.edit', compact('event', 'departments', 'statuses', 'mediaLibrary'));
    }

    public function update(Request $request, Event $event)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to update this event.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'image_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_cropped' => 'nullable|string',
            'image_media_id' => ['nullable', $this->eventMediaRule(MediaType::IMAGE)],
            'status' => 'required|in:' . implode(',', array_map(fn ($case) => $case->value, EventStatus::cases())),
            'department_id' => 'nullable|exists:departments,id',
            'video_link' => 'nullable|url',
            'video_media_id' => ['nullable', $this->eventMediaRule(MediaType::VIDEO)],
            'description_heading' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data = $this->applyEventMediaSelections($request, $request->except(['image_source', 'image_cropped']), $event);

        $event->update($data);

        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $this->deleteEventRecord($event);

        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:events,id'],
        ]);

        $events = Event::whereIn('id', $validated['selected_ids'])->get();

        foreach ($events as $event) {
            $this->deleteEventRecord($event);
        }

        return redirect()->route('dashboard.events.index')
            ->with('success', $events->count() . ' event(s) deleted successfully.');
    }

    private function deleteEventRecord(Event $event): void
    {
        if ($event->image && !$event->image_media_id) {
            $this->cloudinaryUploadService->deleteByUrl($event->image, 'image');
        }

        $event->delete();
    }

    private function eventMediaLibrary(): array
    {
        $media = Media::query()
            ->where('category', MediaCategory::EVENT)
            ->where('upload_status', MediaUploadStatus::READY)
            ->orderByDesc('created_at')
            ->get();

        return [
            'images' => $media->where('media_type', MediaType::IMAGE),
            'videos' => $media->where('media_type', MediaType::VIDEO),
        ];
    }

    private function eventMediaRule(MediaType $type)
    {
        return Rule::exists('media', 'id')->where(function ($query) use ($type) {
            $query
                ->where('category', MediaCategory::EVENT->value)
                ->where('media_type', $type->value)
                ->where('upload_status', MediaUploadStatus::READY->value);
        });
    }

    private function applyEventMediaSelections(Request $request, array $validated, ?Event $event = null): array
    {
        $imageMedia = !empty($validated['image_media_id']) ? Media::find($validated['image_media_id']) : null;
        $videoMedia = !empty($validated['video_media_id']) ? Media::find($validated['video_media_id']) : null;

        if ($request->hasFile('image_source')) {
            $request->validate([
                'image_cropped' => ['required', 'string'],
            ]);

            if ($event && $event->image && !$event->image_media_id) {
                $this->cloudinaryUploadService->deleteByUrl($event->image, 'image');
            }

            $createdMedia = $this->createEventImageMedia($request, $validated['title']);
            $validated['image'] = $createdMedia->file_path;
            $validated['image_media_id'] = $createdMedia->id;
        } elseif ($imageMedia) {
            if ($event && $event->image && !$event->image_media_id && $event->image !== $imageMedia->file_path) {
                $this->cloudinaryUploadService->deleteByUrl($event->image, 'image');
            }

            $validated['image'] = $imageMedia->file_path;
            $validated['image_media_id'] = $imageMedia->id;
        } elseif ($event && $event->image) {
            $validated['image'] = $event->image;
            $validated['image_media_id'] = $event->image_media_id;
        }

        if ($videoMedia) {
            $validated['video_link'] = $videoMedia->youtube_video_url ?: $videoMedia->public_video_url;
            $validated['video_media_id'] = $videoMedia->id;
        } elseif (!blank($request->input('video_link'))) {
            $validated['video_link'] = $request->input('video_link');
            $validated['video_media_id'] = null;
        } elseif ($event) {
            $validated['video_link'] = $event->video_link;
            $validated['video_media_id'] = $event->video_media_id;
        }

        unset($validated['image_source'], $validated['image_cropped']);

        return $validated;
    }

    private function createEventImageMedia(Request $request, string $title): Media
    {
        $uploaded = $this->croppedImageUploadService
            ->storeFromDataUrl($request->string('image_cropped')->toString(), 'events', 'event-image', 'image_cropped');

        $media = new Media([
            'title' => $title,
            'file_name' => $uploaded['original_name'],
            'file_path' => $uploaded['url'],
            'size' => $uploaded['size'],
            'media_type' => MediaType::IMAGE,
            'category' => MediaCategory::EVENT,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
            'upload_last_error' => null,
            'upload_queued_at' => null,
            'upload_completed_at' => now(),
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
            'thumbnail_path' => null,
            'youtube_source_path' => null,
        ]);

        auth()->user()->media()->save($media);

        return $media;
    }
}
