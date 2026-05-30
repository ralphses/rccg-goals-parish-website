<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\YearlyDetail;
use App\Models\Event;
use App\Models\Sermon;
use App\Models\Announcement;
use App\Models\Testimony;
use App\Models\Media;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $yearlyDetail = YearlyDetail::first();

        $profileFields = [
            'phone',
            'avatar',
            'address',
            'what_attracted_you',
            'state_of_origin',
            'occupation',
            'hobbies',
            'favourite_quote',
            'birthday'
        ];

        $filledFields = 0;
        foreach ($profileFields as $field) {
            if (!empty($user->{$field})) {
                $filledFields++;
            }
        }

        $profileCompletion = (count($profileFields) > 0) ? ($filledFields / count($profileFields)) * 100 : 0;

        $latestAnnouncement = Announcement::where(['is_approved' => false, 'is_active' => true])->latest()->first();
        // dd($latestAnnouncement);

        $dashboard = [
            "user" => $user,
            "yearlyDetail" => $yearlyDetail,
            "profileCompletion" => round($profileCompletion),
            "upcomingEvents" => Event::where('date', '>=', now())->orderBy('date', 'asc')->take(3)->get(),
            "latestSermon" => Sermon::latest()->first(),
            "latestAnnouncement" => Announcement::where('status', 'pending')->latest()->first(),
            "latestTestimonies" => Testimony::where('is_approved', false)->latest()->take(3)->get(),
            "latestUser" => User::latest()->first(),
            "latestMedia" => Media::latest()->take(5)->get(),
        ];

        return view('dashboard.dashboard', compact('dashboard'));
    }
}