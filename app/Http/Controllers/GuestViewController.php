<?php

namespace App\Http\Controllers;

use App\Enums\MediaCategory;
use App\Models\YearlyDetail;
use App\Models\Stream;
use App\Models\Testimony;
use App\Models\Sermon;
use App\Models\Media;
use App\Models\Event;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use StreamBucket;

class GuestViewController extends Controller
{
    // In app/Http/Controllers/GuestViewController.php

    public function home()
    {
        $theme = YearlyDetail::latest()->first();
        $stream = Stream::where('is_live', true)->first();
        $testimonies = Testimony::where('is_approved', true)->latest()->take(3)->get();
        $sermons = Sermon::latest()->take(3)->get();
        $galleries = Media::where(['category' => MediaCategory::CHURCH_GALLERY, 'media_type' => 'image'])->latest()->take(6)->get();
        // dd($galleries);
        $events = Event::where('date', '>=', now())->orderBy('date', 'asc')->take(3)->get();
        return view('guest.home', compact('theme', 'stream', 'testimonies', 'sermons', 'galleries', 'events'));
    }

    public function about()
    {
        return view('guest/about');
    }

    public function sermons()
    {
        $videoSermons = Sermon::whereNotNull('video_url')->latest()->paginate(3, ['*'], 'video_page');
        $audioSermons = Sermon::whereNotNull('audio_url')->latest()->paginate(3, ['*'], 'audio_page');

        return view('guest.sermons', compact('videoSermons', 'audioSermons'));
    }

    public function contact()
    {
        return view('guest/contact');
    }

    public function blog()
    {
        return view('guest/blog');
    }

    public function events(Request $request)
    {
        $query = Event::query();

        // Search logic
        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('location', 'like', "%{$searchQuery}%");
            });
        }

        // Sort logic
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'date':
                $query->orderBy('event_date', 'asc');
                break;
            case 'location':
                $query->orderBy('location', 'asc');
                break;
            default:
                $query->latest('event_date');
                break;
        }

        $events = $query->paginate(6)->withQueryString();

        return view('guest.events', compact('events', 'sort'));
    }

    public function event(\App\Models\Event $event)
    {
        return view('guest.event', compact('event'));
    }

    public function departments(Request $request)
    {
        $query = \App\Models\Department::with('leader');

        // Search logic
        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where('name', 'like', "%{$searchQuery}%");
        }

        // Sort logic
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $departments = $query->paginate(6)->withQueryString();

        return view('guest.departments', compact('departments', 'sort'));
    }

    public function department(\App\Models\Department $department)
    {
        $department->load('leader');

        return view('guest.department', compact('department'));
    }

    public function media()
    {
        $galleryMedia = Media::where('category', MediaCategory::CHURCH_GALLERY)
            ->latest()
            ->paginate(8, ['*'], 'gallery_page');

        $testimonyMedia = Media::where('category', MediaCategory::TESTIMONY)
            ->latest()
            ->paginate(8, ['*'], 'testimony_page');

        return view('guest.media', compact('galleryMedia', 'testimonyMedia'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactFormMail($data));

        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }

}
