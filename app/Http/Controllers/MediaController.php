<?php

namespace App\Http\Controllers;

use App\Enums\MediaCategory;
use App\Enums\MediaType;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        return view('dashboard.media.index', compact('media', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = MediaCategory::cases();
        return view('dashboard.media.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt,mp3,wav|max:20480', // max 20MB
            'category' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, MediaCategory::cases())),
            'is_public' => 'boolean',
        ]);

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
            'title' => $request->title,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'size' => $fileSize,
            'media_type' => $mediaType,
            'category' => $request->category,
            'is_public' => $request->input('is_public', 0),
        ]);

        auth()->user()->media()->save($media);

        return redirect()->route('dashboard.media.index')->with('success', 'Media uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return view('dashboard.media.show', compact('media'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        $categories = MediaCategory::cases();
        return view('dashboard.media.edit', compact('media', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, MediaCategory::cases())),
            'is_public' => 'boolean',
            'file' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,qt,mp3,wav|max:20480', // max 20MB
        ]);

        $data = [
            'title' => $request->title,
            'category' => $request->category,
            'is_public' => $request->input('is_public', 0),
        ];

        if ($request->hasFile('file')) {
            // Delete the old file
            Storage::disk('public')->delete($media->file_path);

            // Store the new file
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

            $data['file_name'] = $fileName;
            $data['file_path'] = $filePath;
            $data['size'] = $fileSize;
            $data['media_type'] = $mediaType;
        }

        $media->update($data);

        return redirect()->route('dashboard.media.index')->with('success', 'Media updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return redirect()->route('dashboard.media.index')->with('success', 'Media deleted successfully.');
    }
}