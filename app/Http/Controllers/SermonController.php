<?php

namespace App\Http\Controllers;

use App\Models\Sermon;
use App\Models\User;
use App\Enums\SermonStatus;
use App\Models\SermonAttachment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SermonController extends Controller
{
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
        return view('dashboard.sermons.create', compact('speakers', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'sermon_date' => 'required|date',
            'duration' => 'nullable|string',
            'speaker_id' => 'required|exists:users,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'audio_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'status' => 'required|in:' . implode(',', SermonStatus::values()),
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($request->title);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('sermons', 'public');
        }

        $sermon = Sermon::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = $validated['slug'] . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('sermon_attachments', $fileName, 'public');

                $sermon->attachments()->create([
                    'file_path' => $filePath,
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
        $speakers = User::where('role', 'pastor')->get();
        $statuses = SermonStatus::cases();

        return view('dashboard.sermons.edit', compact('sermon', 'speakers', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sermon $sermon)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'sermon_date' => 'required|date',
            'duration' => 'nullable|string',
            'speaker_id' => 'required|exists:users,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'audio_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'status' => 'required|in:' . implode(',', SermonStatus::values()),
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
        ]);


        $validated['slug'] = $this->generateUniqueSlug($request->title, $sermon->id);

        if ($request->hasFile('cover_image')) {
            // Delete old image if it exists
            if ($sermon->cover_image && Storage::disk('public')->exists($sermon->cover_image)) {
                Storage::disk('public')->delete($sermon->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('sermons', 'public');
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = $validated['slug'] . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('sermon_attachments', $fileName, 'public');

                $sermon->attachments()->create([
                    'file_path' => $filePath,
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
        $sermon->delete();

        return redirect()->route('dashboard.sermons.index')->with('success', 'Sermon deleted successfully.');
    }

    /**
     * Download a sermon attachment.
     */
    public function downloadAttachment(SermonAttachment $attachment)
    {
        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
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
}