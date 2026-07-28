<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\enums\EventStatus;
use App\Models\Event;
use App\Services\CloudinaryUploadService;
use App\Services\CroppedImageUploadService;
use Illuminate\Http\Request;

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
        return view('dashboard.events.create', compact('departments', 'statuses'));
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
            'status' => 'required|in:upcoming,ongoing,past,cancelled',
            'department_id' => 'nullable|exists:departments,id',
            'video_link' => 'nullable|url',
            'description_heading' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image_source')) {
            $request->validate([
                'image_cropped' => ['required', 'string'],
            ]);

            $validated['image'] = $this->croppedImageUploadService
                ->storeFromDataUrl($request->string('image_cropped')->toString(), 'events', 'event-image', 'image_cropped')['url'];
        }

        unset($validated['image_source'], $validated['image_cropped']);

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
        return view('dashboard.events.edit', compact('event', 'departments', 'statuses'));
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
            'status' => 'required|in:upcoming,ongoing,past,cancelled',
            'department_id' => 'nullable|exists:departments,id',
            'video_link' => 'nullable|url',
            'description_heading' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['image_source', 'image_cropped']);

        if ($request->hasFile('image_source')) {
            $request->validate([
                'image_cropped' => ['required', 'string'],
            ]);

            if ($event->image) {
                $this->cloudinaryUploadService->deleteByUrl($event->image, 'image');
            }

            $data['image'] = $this->croppedImageUploadService
                ->storeFromDataUrl($request->string('image_cropped')->toString(), 'events', 'event-image', 'image_cropped')['url'];
        }

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
        if ($event->image) {
            $this->cloudinaryUploadService->deleteByUrl($event->image, 'image');
        }

        $event->delete();
    }
}
