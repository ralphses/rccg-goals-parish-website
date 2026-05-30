<?php

namespace App\Http\Controllers;

use App\Models\Testimony;
use Illuminate\Http\Request;
use App\Enums\TestimonyAnnouncementType;
use App\Enums\MediaCategory;
use App\Enums\MediaType;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class TestimonyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonies = Testimony::latest()->paginate(12);
        return view('dashboard.testimonies.index', compact('testimonies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = MediaCategory::cases();
        $announcementTypes = TestimonyAnnouncementType::cases();
        return view('dashboard.testimonies.create', compact('categories', 'announcementTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'testifier_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required_if:announcement_type,text|nullable|string',
            'testifier_email' => 'nullable|email',
            'testifier_phone' => 'nullable|string',
            'announcement_type' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, TestimonyAnnouncementType::cases())),
            'file' => 'required_if:announcement_type,video,audio|nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt,mp3,wav|max:20480', // max 20MB
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
            'announce_in_service' => 'boolean',
        ]);

        $testimony = Testimony::create($request->except('file'));

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('media', $fileName, 'public');
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            $mediaType = match (true) {
                str_starts_with($mimeType, 'image/') => MediaType::IMAGE,
                str_starts_with($mimeType, 'video/') => MediaType::VIDEO,
                str_starts_with($mimeType, 'audio/') => MediaType::AUDIO,
                default => MediaType::IMAGE,
            };

            $media = new Media([
                'title' => $testimony->title,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'size' => $fileSize,
                'media_type' => $mediaType,
                'category' => MediaCategory::TESTIMONY,
                'is_public' => true,
            ]);

            $testimony->media()->save($media);
        }

        return redirect()->route('dashboard.testimonies.index')->with('success', 'Testimony created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimony $testimony)
    {
        return view('dashboard.testimonies.show', compact('testimony'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimony $testimony)
    {
        $announcementTypes = TestimonyAnnouncementType::cases();
        return view('dashboard.testimonies.edit', compact('testimony', 'announcementTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimony $testimony)
    {
        $request->validate([
            'testifier_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required_if:announcement_type,text|nullable|string',
            'testifier_email' => 'nullable|email',
            'testifier_phone' => 'nullable|string',
            'announcement_type' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, TestimonyAnnouncementType::cases())),
            'file' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt,mp3,wav|max:102400', // max 100MB
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
            'announce_in_service' => 'boolean',
        ]);

        $testimony->update($request->except('file'));

        if ($request->hasFile('file')) {
            // Delete old media if it exists
            if ($testimony->media->isNotEmpty()) {
                foreach ($testimony->media as $media) {
                    Storage::disk('public')->delete($media->file_path);
                    $media->delete();
                }
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('media', $fileName, 'public');
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            $mediaType = match (true) {
                str_starts_with($mimeType, 'image/') => MediaType::IMAGE,
                str_starts_with($mimeType, 'video/') => MediaType::VIDEO,
                str_starts_with($mimeType, 'audio/') => MediaType::AUDIO,
                default => MediaType::IMAGE,
            };

            $media = new Media([
                'title' => $testimony->title,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'size' => $fileSize,
                'media_type' => $mediaType,
                'category' => MediaCategory::TESTIMONY,
                'is_public' => true,
            ]);

            $testimony->media()->save($media);
        }

        return redirect()->route('dashboard.testimonies.index')->with('success', 'Testimony updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimony $testimony)
    {
        //
    }

    public function approve(Testimony $testimony)
    {
        $testimony->update(['is_approved' => true]);
        return redirect()->route('dashboard')->with('success', 'Testimony approved successfully.');
    }
}