<?php

namespace App\Http\Controllers;

use App\Models\BirthdayBoardComment;
use App\Models\BirthdayGreeting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BirthdayBoardCommentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->isActive(), 403, 'Only active church accounts can post birthday wishes.');

        $today = now('Africa/Lagos')->startOfDay();
        abort_unless(BirthdayGreeting::query()->publicForDate($today)->exists(), 422, 'There are no published birthday greetings to comment on today.');

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        BirthdayBoardComment::create([
            'user_id' => $user->id,
            'celebration_date' => $today->toDateString(),
            'message' => trim($validated['message']),
        ]);

        return redirect()->route('birthdays')->with('success', 'Your birthday wish has been shared.');
    }

    public function destroy(Request $request, BirthdayBoardComment $birthdayBoardComment): RedirectResponse
    {
        $user = $request->user();
        $canManageStudio = $user && ($user->isAdmin() || $user->isMedia() || $user->leadsActiveDepartment());

        abort_unless($user && ($birthdayBoardComment->user_id === $user->id || $canManageStudio), 403);

        $birthdayBoardComment->delete();

        return redirect()->route('birthdays')->with('success', 'Birthday wish removed.');
    }
}
