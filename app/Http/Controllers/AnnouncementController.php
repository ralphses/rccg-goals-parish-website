<?php

namespace App\Http\Controllers;

use App\Enums\AnnouncementFrequency;
use App\Models\Announcement;
use App\Models\Media;
use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(12);
        return view('dashboard.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $frequencies = AnnouncementFrequency::cases();
        return view('dashboard.announcements.create', compact('frequencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'service_date' => 'required|date',
            'frequency' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, AnnouncementFrequency::cases())),
            'is_active' => 'boolean',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:102400', // 100MB Max for each file
        ]);

        $announcement = new Announcement($request->except('is_approved'));
        $announcement->user_id = auth()->id();
        $announcement->save();

        // Notify admins and editors
        $usersToNotify = User::whereIn('role', ['admin', 'editor'])->get();
        foreach ($usersToNotify as $user) {
            AppNotification::create([
                'user_id' => $user->id,
                'title' => 'New Announcement Request',
                'message' => 'A new announcement "' . $announcement->title . '" has been submitted for approval.',
                'link' => route('dashboard.announcements.show', $announcement->id),
            ]);
        }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('announcements', 'public');

                $announcement->media()->create([
                    'title' => $announcement->title,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'size' => $file->getSize(),
                    'media_type' => $this->getMediaType($file->getMimeType()),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('dashboard.announcements.index')->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        return view('dashboard.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $frequencies = AnnouncementFrequency::cases();
        return view('dashboard.announcements.edit', compact('announcement', 'frequencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'service_date' => 'required|date',
            'frequency' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, AnnouncementFrequency::cases())),
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:102400', // 100MB Max for each file
        ]);

        $announcement->update($request->except('media'));

        if ($request->hasFile('media')) {
            // Delete old media if it exists
            if ($announcement->media->isNotEmpty()) {
                foreach ($announcement->media as $media) {
                    Storage::disk('public')->delete($media->file_path);
                    $media->delete();
                }
            }

            foreach ($request->file('media') as $file) {
                $path = $file->store('announcements', 'public');

                $announcement->media()->create([
                    'title' => $announcement->title,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'size' => $file->getSize(),
                    'media_type' => $this->getMediaType($file->getMimeType())
                ]);
            }
        }

        return redirect()->route('dashboard.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('dashboard.announcements.index')->with('success', 'Announcement deleted successfully.');
    }

    public function approve(Announcement $announcement)
    {
        // $this->authorize('approve', $announcement);

        $announcement->update(['is_approved' => true]);

        // Notify the creator
        AppNotification::create([
            'user_id' => $announcement->user_id,
            'title' => 'Announcement Approved',
            'message' => 'Your announcement "' . $announcement->title . '" has been approved.',
            'link' => route('dashboard.announcements.show', $announcement->id),
        ]);

        return redirect()->route('dashboard.announcements.show', $announcement->id)->with('success', 'Announcement approved successfully.');
    }

    private function getMediaType(string $mimeType)
    {
        if (str_starts_with($mimeType, 'image')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video')) {
            return 'video';
        }

        return 'document';
    }
}