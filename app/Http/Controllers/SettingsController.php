<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\YearlyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string|max:255',
            'what_attracted_you' => 'nullable|string',
            'state_of_origin' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'hobbies' => 'nullable|string',
            'favourite_quote' => 'nullable|string',
            'birthday' => 'nullable|date',
        ]);

        $data = $request->except('avatar');

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
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
}