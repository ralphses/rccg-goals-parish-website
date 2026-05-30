<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:upcoming,ongoing,past,cancelled',
            'department_id' => 'nullable|exists:departments,id',
            'video_link' => 'nullable|url',
            'description_heading' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        return view('dashboard.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $departments = Department::all();
        $statuses = EventStatus::cases();
        return view('dashboard.events.edit', compact('event', 'departments', 'statuses'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('dashboard.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}