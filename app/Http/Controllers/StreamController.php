<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function index()
    {
        $stream = Stream::first();
        return view('dashboard.stream.index', compact('stream'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'youtube_url' => 'required|url',
            'is_live' => 'sometimes|boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $validatedData['is_live'] = $request->has('is_live');

        $stream = Stream::first();
        if ($stream) {
            $stream->update($validatedData);
        } else {
            Stream::create($validatedData);
        }

        return redirect()->route('dashboard.stream.index')->with('success', 'Stream settings updated successfully.');
    }
}