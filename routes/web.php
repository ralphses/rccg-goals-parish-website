<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SermonAttachmentController;
use App\Http\Controllers\SermonController;
use App\Http\Controllers\TestimonyController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestViewController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

// PUBLIC ROUTES
Route::get('/', [GuestViewController::class, 'home'])->name('home');

Route::get('/login', function () {
    return view('guest/login');
})->name('login');

Route::get('/about', [GuestViewController::class, 'about'])->name('about');

Route::get('/sermons', [GuestViewController::class, 'sermons'])->name('sermons');

Route::get("/contact", [GuestViewController::class, 'contact'])->name('contact');
Route::post("/contact", [GuestViewController::class, 'store'])->name('contact.store');

Route::get("/blog", [GuestViewController::class, 'blog'])->name('blog');

Route::get("/events", [GuestViewController::class, 'events'])->name('events');

Route::get("/events/{event}", [GuestViewController::class, 'event'])->name('event');

Route::get("/departments", [GuestViewController::class, 'departments'])->name('departments');

Route::get("/departments/{department}", [GuestViewController::class, 'department'])->name('department');

Route::get("/media", [GuestViewController::class, 'media'])->name('media');

// AUTHENTICATED ROUTES
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

    // Add this inside the 'auth' middleware group for the dashboard
    Route::middleware([RoleMiddleware::class . ':admin,pastor,media'])->group(function () {
        Route::get('/stream', [StreamController::class, 'index'])->name('dashboard.stream.index');
        Route::patch('/stream', [StreamController::class, 'update'])->name('dashboard.stream.update');
    });

    // USERS ROUTES
    Route::middleware([RoleMiddleware::class . ':admin,pastor'])->prefix("users")->group(function () {
        Route::delete('/bulk-delete', [UserController::class, 'bulkDestroy'])
            ->name('dashboard.users.bulk-destroy');

        Route::resource('', UserController::class)
            ->parameters(['' => 'user'])
            ->names([
                'index' => 'dashboard.users.index',
                'create' => 'dashboard.users.create',
                'store' => 'dashboard.users.store',
                'show' => 'dashboard.users.show',
                'edit' => 'dashboard.users.edit',
                'update' => 'dashboard.users.update',
                'destroy' => 'dashboard.users.destroy'
            ]);

        Route::get('/{user}/departments', [UserController::class, 'departments'])
            ->name('dashboard.users.departments');

        Route::post('/{user}/departments', [UserController::class, 'updateDepartments'])
            ->name('dashboard.users.departments.update');

        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('dashboard.users.password.reset');

        Route::get('/{user}/message', [UserController::class, 'message'])
            ->name('dashboard.users.message');
    });


    // EVENTS ROUTES
    Route::prefix("events")->group(function () {
        Route::delete('/bulk-delete', [EventController::class, 'bulkDestroy'])
            ->name('dashboard.events.bulk-destroy');

        Route::resource('', EventController::class)
            ->parameters(['' => 'event'])
            ->names([
                'index' => 'dashboard.events.index',
                'create' => 'dashboard.events.create',
                'store' => 'dashboard.events.store',
                'show' => 'dashboard.events.show',
                'edit' => 'dashboard.events.edit',
                'update' => 'dashboard.events.update',
                'destroy' => 'dashboard.events.destroy'
            ]);
    });


    // DEPARTMENTS ROUTES
    Route::prefix("departments")->group(function () {
        Route::delete('/bulk-delete', [DepartmentController::class, 'bulkDestroy'])
            ->name('dashboard.departments.bulk-destroy');

        Route::resource('', DepartmentController::class)
            ->parameters(['' => 'department'])
            ->names([
                'index' => 'dashboard.departments.index',
                'create' => 'dashboard.departments.create',
                'store' => 'dashboard.departments.store',
                'show' => 'dashboard.departments.show',
                'edit' => 'dashboard.departments.edit',
                'update' => 'dashboard.departments.update',
                'destroy' => 'dashboard.departments.destroy'
            ]);
    });


    // SERMONS ROUTES
    Route::prefix("sermons")->group(function () {
        Route::delete('/bulk-delete', [SermonController::class, 'bulkDestroy'])
            ->name('dashboard.sermons.bulk-destroy');

        Route::resource('', SermonController::class)
            ->parameters(['' => 'sermon'])
            ->names([
                'index' => 'dashboard.sermons.index',
                'create' => 'dashboard.sermons.create',
                'store' => 'dashboard.sermons.store',
                'show' => 'dashboard.sermons.show',
                'edit' => 'dashboard.sermons.edit',
                'update' => 'dashboard.sermons.update',
                'destroy' => 'dashboard.sermons.destroy'
            ]);
    });


    Route::delete('sermon-attachments/{attachment}', [SermonAttachmentController::class, 'destroy'])
        ->name('dashboard.sermon-attachments.destroy');

    Route::get('sermon-attachments/{attachment}/download', [SermonController::class, 'downloadAttachment'])
        ->name('dashboard.sermon-attachments.download');


    // MEDIA ROUTES
    Route::middleware([RoleMiddleware::class . ':admin,pastor,media,editor'])->prefix("media")->group(function () {
        Route::delete('/bulk-delete', [MediaController::class, 'bulkDestroy'])
            ->name('dashboard.media.bulk-destroy');

        Route::resource('', MediaController::class)
            ->parameters(['' => 'media'])
            ->names([
                'index' => 'dashboard.media.index',
                'create' => 'dashboard.media.create',
                'store' => 'dashboard.media.store',
                'show' => 'dashboard.media.show',
                'edit' => 'dashboard.media.edit',
                'update' => 'dashboard.media.update',
                'destroy' => 'dashboard.media.destroy'
            ]);

        Route::post('/{media}/youtube/retry', [MediaController::class, 'retryYouTubePublish'])
            ->name('dashboard.media.youtube.retry');
        Route::post('/{media}/upload/retry', [MediaController::class, 'retryVideoUpload'])
            ->name('dashboard.media.upload.retry');
    });


    // TESTIMONIES ROUTES
    Route::prefix("testimonies")->group(function () {
        Route::delete('/bulk-delete', [TestimonyController::class, 'bulkDestroy'])
            ->name('dashboard.testimonies.bulk-destroy');

        Route::resource('', TestimonyController::class)
            ->parameters(['' => 'testimony'])
            ->names([
                'index' => 'dashboard.testimonies.index',
                'create' => 'dashboard.testimonies.create',
                'store' => 'dashboard.testimonies.store',
                'show' => 'dashboard.testimonies.show',
                'edit' => 'dashboard.testimonies.edit',
                'update' => 'dashboard.testimonies.update',
                'destroy' => 'dashboard.testimonies.destroy'
            ]);

        Route::patch('/{testimony}/approve', [TestimonyController::class, 'approve'])
            ->name('dashboard.testimonies.approve');
    });


    // ANNOUNCEMENTS ROUTES
    Route::middleware([RoleMiddleware::class . ':admin,pastor,media'])->prefix("announcements")->group(function () {
        Route::delete('/bulk-delete', [AnnouncementController::class, 'bulkDestroy'])
            ->name('dashboard.announcements.bulk-destroy');

        Route::resource('', AnnouncementController::class)
            ->parameters(['' => 'announcement'])
            ->names([
                'index' => 'dashboard.announcements.index',
                'create' => 'dashboard.announcements.create',
                'store' => 'dashboard.announcements.store',
                'show' => 'dashboard.announcements.show',
                'edit' => 'dashboard.announcements.edit',
                'update' => 'dashboard.announcements.update',
                'destroy' => 'dashboard.announcements.destroy'
            ]);

        Route::patch('/{announcement}/approve', [AnnouncementController::class, 'approve'])
            ->name('dashboard.announcements.approve');

        Route::patch('/{announcement}/decline', [AnnouncementController::class, 'decline'])
            ->name('dashboard.announcements.decline');
    });


    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');

    Route::patch('/settings', [SettingsController::class, 'updateUserProfile'])
        ->name('settings.update');

    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('settings.password.update');

    Route::patch('/settings/yearly-details', [SettingsController::class, 'updateYearlyDetails'])
        ->name('settings.yearly-details.update');

    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/settings/youtube/connect', [SettingsController::class, 'connectYouTube'])
            ->name('settings.youtube.connect');
        Route::delete('/settings/youtube', [SettingsController::class, 'disconnectYouTube'])
            ->name('settings.youtube.disconnect');
    });


    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('password/change', [PasswordChangeController::class, 'show'])
        ->name('password.change');

    Route::post('password/change', [PasswordChangeController::class, 'update'])
        ->name('password.change.update');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/integrations/youtube/callback', [SettingsController::class, 'handleYouTubeCallback'])
            ->middleware('verified')
            ->name('settings.youtube.callback');
        Route::get('/dashboard/settings/youtube/callback', [SettingsController::class, 'handleYouTubeCallback'])
            ->middleware('verified');
    });
});

require __DIR__ . '/auth.php';

// URL::forceScheme('https');
