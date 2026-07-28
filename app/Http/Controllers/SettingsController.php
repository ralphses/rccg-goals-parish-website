<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\YouTubeIntegration;
use App\Models\YearlyDetail;
use App\Services\CloudinaryUploadService;
use App\Services\CroppedImageUploadService;
use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function __construct(
        private CloudinaryUploadService $cloudinaryUploadService,
        private CroppedImageUploadService $croppedImageUploadService,
        private YouTubeService $youTubeService
    )
    {
    }
    /**
     * Display the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = User::find(auth()->id());
        $yearlyDetail = YearlyDetail::first();

        if (!$yearlyDetail) {
            $yearlyDetail = YearlyDetail::create([
                'current_year' => now()->year,
                'year_theme' => 'Theme of the Year',
                'year_scripture' => 'Scripture of the Year',
                'current_month' => now()->monthName,
                'current_month_theme' => 'Theme of the Month',
                'current_month_scripture' => 'Scripture of the Month',
            ]);
        }

        $years = range(2026, now()->year + 5);
        $months = collect(range(1, 12))->map(function ($month) {
            return date('F', mktime(0, 0, 0, $month, 1));
        });

        $settings = [
            "user" => $user,
            "yearlyDetail" => $yearlyDetail,
            "years" => $years,
            "months" => $months,
            "youtubeIntegration" => YouTubeIntegration::query()->first(),
        ];

        return view('dashboard.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'avatar_source' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'avatar_cropped' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'what_attracted_you' => 'nullable|string',
            'state_of_origin' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'hobbies' => 'nullable|string',
            'favourite_quote' => 'nullable|string',
            'birthday' => 'nullable|date',
        ]);

        $data = $request->except(['avatar_source', 'avatar_cropped']);

        if ($request->hasFile('avatar_source')) {
            $request->validate([
                'avatar_cropped' => ['required', 'string'],
            ]);

            if ($user->avatar) {
                $this->cloudinaryUploadService->deleteByUrl($user->avatar, 'image');
            }
            $data['avatar'] = $this->croppedImageUploadService
                ->storeFromDataUrl($request->string('avatar_cropped')->toString(), 'avatars', 'avatar', 'avatar_cropped')['url'];
        }

        $user->update($data);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'old_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided password does not match your current password.');
                }
            }],
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ]);
        

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('settings.index')->with('success', 'Password updated successfully.');
    }

    public function updateYearlyDetails(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isPastor()) {
            return back()->with('error', 'You are not authorized to update yearly details.');
        }

        $validatedData = $request->validate([
            'current_year' => 'required|integer',
            'year_theme' => 'required|string|max:255',
            'year_scripture' => 'required|string|max:255',
            'current_month' => 'required|string|max:255',
            'current_month_theme' => 'required|string|max:255',
            'current_month_scripture' => 'required|string|max:255',
            'current_month_scripture_content' => 'required|string|max:255',
            'year_scripture_content' => 'required|string|max:255',
        ]);

        $yearlyDetail = YearlyDetail::first();
        $yearlyDetail->update($validatedData);

        return redirect()->route('settings.index')->with('success', 'Yearly details updated successfully.');
    }

    public function connectYouTube(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $state = (string) str()->uuid();
        $request->session()->put('youtube_oauth_state', $state);

        return redirect()->away($this->youTubeService->authorizationUrl($state));
    }

    public function handleYouTubeCallback(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $expectedState = $request->session()->pull('youtube_oauth_state');

        if (!$expectedState || $expectedState !== $request->string('state')->toString()) {
            return redirect()->route('settings.index')->with('error', 'Unable to verify the YouTube connection request.');
        }

        if ($request->filled('error')) {
            return redirect()->route('settings.index')->with('error', 'YouTube connection was not completed.');
        }

        $tokens = $this->youTubeService->exchangeCodeForTokens($request->string('code')->toString());
        $channel = $this->youTubeService->fetchChannel($tokens['access_token']);

        YouTubeIntegration::query()->updateOrCreate(
            ['id' => optional(YouTubeIntegration::query()->first())->id ?? 1],
            [
                'channel_id' => $channel['channel_id'],
                'channel_title' => $channel['channel_title'],
                'channel_thumbnail_url' => $channel['channel_thumbnail_url'],
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_expires_at' => now()->addSeconds($tokens['expires_in']),
                'last_used_at' => now(),
                'last_error' => null,
                'connected_by' => $request->user()->id,
            ]
        );

        return redirect()->route('settings.index')->with('success', 'YouTube channel connected successfully.');
    }

    public function disconnectYouTube(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $integration = YouTubeIntegration::query()->first();

        if ($integration) {
            $integration->delete();
        }

        return redirect()->route('settings.index')->with('success', 'YouTube channel disconnected successfully.');
    }
}
