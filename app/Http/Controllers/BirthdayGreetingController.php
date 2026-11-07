<?php

namespace App\Http\Controllers;

use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\UserStatus;
use App\Models\BirthdayGreeting;
use App\Models\Media;
use App\Models\User;
use App\Services\BirthdayCardService;
use App\Services\CloudinaryUploadService;
use App\Support\MediaUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BirthdayGreetingController extends Controller
{
    public function __construct(
        private BirthdayCardService $birthdayCardService,
        private CloudinaryUploadService $cloudinaryUploadService,
    ) {}

    public function index(Request $request)
    {
        $this->authorizeStudio($request);
        $today = now('Africa/Lagos')->startOfDay();
        $greetings = BirthdayGreeting::with('media')
            ->whereDate('celebration_date', $today)
            ->get()
            ->keyBy('user_id');
        $celebrants = $this->todayCelebrants($today);

        return view('dashboard.birthdays.index', compact('today', 'celebrants', 'greetings'));
    }

    public function generate(Request $request, User $user): RedirectResponse
    {
        $this->authorizeStudio($request);
        $today = now('Africa/Lagos')->startOfDay();
        abort_unless($this->todayCelebrants($today)->contains('id', $user->id), 404);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:280'],
        ]);
        $message = trim($validated['message'] ?? '') ?: BirthdayCardService::DEFAULT_MESSAGE;
        // SQLite may retain a date column as a midnight datetime. Match by date
        // so an existing daily greeting is regenerated instead of duplicated.
        $greeting = BirthdayGreeting::query()
            ->where('user_id', $user->id)
            ->whereDate('celebration_date', $today)
            ->first() ?? new BirthdayGreeting([
                'user_id' => $user->id,
                'celebration_date' => $today->toDateString(),
            ]);
        $wasPublished = $greeting->isPublished();
        $oldMedia = $greeting->media;
        $oldMediaPath = $oldMedia?->file_path;
        $binary = $this->birthdayCardService->render($user, $message);
        $upload = $this->cloudinaryUploadService->uploadDataUrl(
            'data:image/jpeg;base64,'.base64_encode($binary),
            'birthdays/'.$today->format('Y'),
            'birthday-'.$user->id.'-'.$today->format('Ymd'),
        );

        $greeting->fill([
            'message' => $message,
            'generated_by' => $request->user()->id,
        ])->save();

        $attributes = [
            'title' => 'Birthday Greeting — '.$user->name.' — '.$today->format('d M Y'),
            'file_name' => $upload['original_name'],
            'file_path' => $upload['url'],
            'thumbnail_path' => null,
            'size' => $upload['size'],
            'category' => MediaCategory::BIRTHDAY,
            'media_type' => MediaType::IMAGE,
            'upload_status' => MediaUploadStatus::READY,
            'upload_completed_at' => now(),
            'is_public' => $wasPublished,
        ];

        $media = $oldMedia ?: new Media;
        $media->fill($attributes);
        $media->mediable()->associate($greeting);
        $media->save();

        $greeting->update(['media_id' => $media->id]);

        if ($oldMediaPath && $oldMediaPath !== $media->file_path) {
            $this->cloudinaryUploadService->deleteByUrl($oldMediaPath, 'image');
        }

        return redirect()->route('dashboard.birthdays.index')->with('success', 'Birthday card generated for '.$user->name.'.');
    }

    public function publish(Request $request, BirthdayGreeting $birthdayGreeting): RedirectResponse
    {
        $this->authorizeStudio($request);
        abort_unless($birthdayGreeting->media, 422, 'Generate a birthday card before publishing it.');
        abort_unless($this->isCurrentBirthday($birthdayGreeting), 422, 'Birthday cards can only be published on the member\'s birthday.');

        $birthdayGreeting->update(['published_at' => now()]);
        $birthdayGreeting->media->update(['is_public' => true]);

        return back()->with('success', 'Birthday card published to the home page.');
    }

    public function unpublish(Request $request, BirthdayGreeting $birthdayGreeting): RedirectResponse
    {
        $this->authorizeStudio($request);

        $birthdayGreeting->update(['published_at' => null]);
        $birthdayGreeting->media?->update(['is_public' => false]);

        return back()->with('success', 'Birthday card removed from the home page.');
    }

    public function download(Request $request, BirthdayGreeting $birthdayGreeting): StreamedResponse
    {
        $this->authorizeStudio($request);
        abort_unless($birthdayGreeting->media?->file_path, 404);
        $media = $birthdayGreeting->media;
        $path = MediaUrl::toStoragePath($media->file_path);
        $filename = 'birthday-'.$birthdayGreeting->user->name.'-'.$birthdayGreeting->celebration_date->format('Y-m-d').'.jpg';

        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, $filename, ['Content-Type' => 'image/jpeg']);
        }

        return response()->streamDownload(function () use ($media) {
            echo Http::timeout(30)->get($media->file_url)->body();
        }, $filename, ['Content-Type' => 'image/jpeg']);
    }

    private function authorizeStudio(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->isMedia() || $user->leadsActiveDepartment()), 403);
    }

    private function todayCelebrants(\Carbon\CarbonInterface $today)
    {
        return User::query()
            ->where('status', UserStatus::ACTIVE->value)
            ->whereNotNull('birthday')
            ->whereMonth('birthday', $today->month)
            ->whereDay('birthday', $today->day)
            ->orderBy('name')
            ->get();
    }

    private function isCurrentBirthday(BirthdayGreeting $greeting): bool
    {
        $today = now('Africa/Lagos');
        $member = $greeting->user;

        return $greeting->celebration_date?->isSameDay($today)
            && $member?->status === UserStatus::ACTIVE
            && $member->birthday?->month === $today->month
            && $member->birthday?->day === $today->day;
    }
}
