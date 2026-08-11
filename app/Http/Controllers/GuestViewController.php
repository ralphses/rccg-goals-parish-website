<?php

namespace App\Http\Controllers;

use App\enums\MediaCategory;
use App\Models\YearlyDetail;
use App\Models\Stream;
use App\Models\Testimony;
use App\Models\Sermon;
use App\Models\Media;
use App\Models\Event;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\YouTubePublishStatus;
use App\Mail\ContactFormMail;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use StreamBucket;

class GuestViewController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {
    }

    public function home()
    {
        $theme = YearlyDetail::latest()->first();
        $stream = Stream::where('is_live', true)->first();
        $testimonies = Testimony::with('media')->where('is_approved', true)->latest()->take(3)->get();
        $sermons = Sermon::with('speaker')
            ->where('status', \App\enums\SermonStatus::PUBLISHED->value)
            ->latest('published_at')
            ->take(3)
            ->get();
        $galleries = Media::where([
                'category' => MediaCategory::CHURCH_GALLERY,
                'media_type' => 'image',
                'is_public' => true,
            ])
            ->where('upload_status', \App\enums\MediaUploadStatus::READY)
            ->latest()
            ->take(6)
            ->get();
        $events = Event::where('event_date', '>=', now())->orderBy('event_date', 'asc')->take(3)->get();
        $seo = $this->seoService->home();

        return view('guest.home', compact('theme', 'stream', 'testimonies', 'sermons', 'galleries', 'events', 'seo'));
    }

    public function about()
    {
        $seo = $this->seoService->about();

        return view('guest/about', compact('seo'));
    }

    public function sermons()
    {
        $videoSermons = Sermon::with('speaker')
            ->where('status', \App\enums\SermonStatus::PUBLISHED->value)
            ->whereNotNull('video_url')
            ->latest('published_at')
            ->paginate(3, ['*'], 'video_page');
        $audioSermons = Sermon::with('speaker')
            ->where('status', \App\enums\SermonStatus::PUBLISHED->value)
            ->whereNotNull('audio_url')
            ->latest('published_at')
            ->paginate(3, ['*'], 'audio_page');
        $seo = $this->seoService->sermonsIndex($videoSermons, $audioSermons);

        return view('guest.sermons', compact('videoSermons', 'audioSermons', 'seo'));
    }

    public function sermon(Sermon $sermon)
    {
        abort_unless(
            $sermon->status === \App\enums\SermonStatus::PUBLISHED || $sermon->status === \App\enums\SermonStatus::PUBLISHED->value,
            404
        );

        $sermon->load('speaker');
        $seo = $this->seoService->sermon($sermon);

        return view('guest.sermon', compact('sermon', 'seo'));
    }

    public function contact()
    {
        $seo = $this->seoService->contact();

        return view('guest/contact', compact('seo'));
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

        $events = $query
            ->where('status', '!=', \App\enums\EventStatus::CANCELLED->value)
            ->paginate(6)
            ->withQueryString();
        $seo = $this->seoService->eventsIndex($events);

        return view('guest.events', compact('events', 'sort', 'seo'));
    }

    public function event(\App\Models\Event $event)
    {
        abort_if(($event->status?->value ?? $event->status) === \App\enums\EventStatus::CANCELLED->value, 404);

        $seo = $this->seoService->event($event);

        return view('guest.event', compact('event', 'seo'));
    }

    public function departments(Request $request)
    {
        $query = \App\Models\Department::with('leader')
            ->where('status', \App\enums\UserStatus::ACTIVE->value);

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
        $seo = $this->seoService->departmentsIndex($departments);

        return view('guest.departments', compact('departments', 'sort', 'seo'));
    }

    public function department(\App\Models\Department $department)
    {
        abort_unless(($department->status?->value ?? $department->status) === \App\enums\UserStatus::ACTIVE->value, 404);

        $department->load('leader');
        $seo = $this->seoService->department($department);

        return view('guest.department', compact('department', 'seo'));
    }

    public function media()
    {
        $galleryMedia = Media::where('category', MediaCategory::CHURCH_GALLERY)
            ->where('is_public', true)
            ->where(function ($query) {
                $query->where('media_type', MediaType::IMAGE->value)
                    ->where('upload_status', MediaUploadStatus::READY->value)
                    ->orWhere(function ($videoQuery) {
                        $videoQuery->where('media_type', MediaType::VIDEO->value)
                            ->where('upload_status', MediaUploadStatus::READY->value)
                            ->where('youtube_status', YouTubePublishStatus::PUBLISHED->value)
                            ->whereNotNull('youtube_video_url');
                    });
            })
            ->latest()
            ->paginate(8, ['*'], 'gallery_page');

        $testimonyMedia = Media::where('category', MediaCategory::TESTIMONY)
            ->where('is_public', true)
            ->where(function ($query) {
                $query->where('media_type', MediaType::IMAGE->value)
                    ->where('upload_status', MediaUploadStatus::READY->value)
                    ->orWhere(function ($videoQuery) {
                        $videoQuery->where('media_type', MediaType::VIDEO->value)
                            ->where('upload_status', MediaUploadStatus::READY->value)
                            ->where('youtube_status', YouTubePublishStatus::PUBLISHED->value)
                            ->whereNotNull('youtube_video_url');
                    });
            })
            ->latest()
            ->paginate(8, ['*'], 'testimony_page');
        $seo = $this->seoService->media($galleryMedia, $testimonyMedia);

        return view('guest.media', compact('galleryMedia', 'testimonyMedia', 'seo'));
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

    public function sitemap()
    {
        $urls = $this->seoService->publicSitemapUrls();

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        return response($this->seoService->robotsTxt(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
