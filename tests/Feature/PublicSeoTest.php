<?php

namespace Tests\Feature;

use App\enums\EventStatus;
use App\enums\MediaCategory;
use App\enums\MediaType;
use App\enums\MediaUploadStatus;
use App\enums\SermonStatus;
use App\enums\UserRole;
use App\enums\UserStatus;
use App\Models\Department;
use App\Models\Event;
use App\Models\Media;
use App\Models\Sermon;
use App\Models\User;
use App\Services\CroppedImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_public_urls_only(): void
    {
        $speaker = User::factory()->create(['role' => UserRole::PASTOR]);
        $sermon = Sermon::factory()->create([
            'speaker_id' => $speaker->id,
            'status' => SermonStatus::PUBLISHED->value,
            'video_url' => 'https://www.youtube.com/watch?v=abc123xyz89',
        ]);
        $draftSermon = Sermon::factory()->create([
            'speaker_id' => $speaker->id,
            'status' => SermonStatus::DRAFT->value,
        ]);
        $department = Department::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $inactiveDepartment = Department::factory()->create(['status' => UserStatus::SUSPENDED->value]);
        $event = Event::factory()->create([
            'department_id' => $department->id,
            'status' => EventStatus::UPCOMING->value,
        ]);
        $cancelledEvent = Event::factory()->create([
            'department_id' => $department->id,
            'status' => EventStatus::CANCELLED->value,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertSee(route('home'), false);
        $response->assertSee(route('sermons.show', $sermon->slug), false);
        $response->assertSee(route('department', $department), false);
        $response->assertSee(route('event', $event), false);
        $response->assertDontSee(route('dashboard'), false);
        $response->assertDontSee(route('sermons.show', $draftSermon->slug), false);
        $response->assertDontSee(route('department', $inactiveDepartment), false);
        $response->assertDontSee(route('event', $cancelledEvent), false);
    }

    public function test_robots_txt_disallows_internal_routes_and_links_sitemap(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertSee('Disallow: /dashboard');
        $response->assertSee('Disallow: /login');
    $response->assertSee('Sitemap: ' . route('sitemap'));
    }

    public function test_sermon_detail_page_renders_canonical_meta_and_schema(): void
    {
        $speaker = User::factory()->create(['role' => UserRole::PASTOR]);
        $sermon = Sermon::factory()->create([
            'speaker_id' => $speaker->id,
            'status' => SermonStatus::PUBLISHED->value,
            'slug' => 'power-for-today',
            'title' => 'Power for Today',
            'description' => 'A sermon for the week.',
            'video_url' => 'https://www.youtube.com/watch?v=abc123xyz89',
        ]);

        $response = $this->get(route('sermons.show', $sermon->slug));

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="' . route('sermons.show', $sermon->slug) . '"', false);
        $response->assertSee('Power for Today | RCCG GOALS Parish', false);
        $response->assertSee('"@type":"VideoObject"', false);
        $response->assertSee('https://www.youtube.com/embed/abc123xyz89', false);
    }

    public function test_event_and_department_pages_use_fallback_meta_when_custom_fields_missing(): void
    {
        $department = Department::factory()->create([
            'name' => 'Ushering',
            'description' => 'Serving worshippers with excellence.',
            'status' => UserStatus::ACTIVE->value,
        ]);

        $event = Event::factory()->create([
            'title' => 'Night of Worship',
            'description' => 'A night of praise and worship.',
            'department_id' => $department->id,
            'status' => EventStatus::UPCOMING->value,
        ]);

        $departmentResponse = $this->get(route('department', $department));
        $departmentResponse->assertOk();
        $departmentResponse->assertSee('Serving worshippers with excellence.', false);

        $eventResponse = $this->get(route('event', $event));
        $eventResponse->assertOk();
        $eventResponse->assertSee('A night of praise and worship.', false);
    }

    public function test_custom_meta_fields_override_fallback_metadata(): void
    {
        $department = Department::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'meta_title' => 'Custom Department Title',
            'meta_description' => 'Custom department description',
            'meta_keywords' => 'custom, department',
        ]);

        $response = $this->get(route('department', $department));

        $response->assertOk();
        $response->assertSee('Custom Department Title', false);
        $response->assertSee('Custom department description', false);
        $response->assertSee('custom, department', false);
    }

    public function test_non_public_media_is_excluded_from_public_media_page(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $publicMedia = new Media([
            'title' => 'Visible Media',
            'file_name' => 'visible.jpg',
            'file_path' => 'https://res.cloudinary.com/example/image/upload/visible.jpg',
            'size' => 100,
            'media_type' => MediaType::IMAGE,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
        ]);

        $privateMedia = new Media([
            'title' => 'Hidden Media',
            'file_name' => 'hidden.jpg',
            'file_path' => 'https://res.cloudinary.com/example/image/upload/hidden.jpg',
            'size' => 100,
            'media_type' => MediaType::IMAGE,
            'category' => MediaCategory::CHURCH_GALLERY,
            'is_public' => false,
            'upload_status' => MediaUploadStatus::READY,
        ]);

        $user->media()->saveMany([$publicMedia, $privateMedia]);

        $response = $this->get(route('media'));

        $response->assertOk();
        $response->assertSee('Visible Media');
        $response->assertDontSee('Hidden Media');
    }

    public function test_events_page_uses_uploaded_event_image_before_legacy_image(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $media = new Media([
            'title' => 'Uploaded Event Image',
            'file_name' => 'uploaded-event.jpg',
            'file_path' => 'https://res.cloudinary.com/example/image/upload/events/uploaded-event.jpg',
            'size' => 100,
            'media_type' => MediaType::IMAGE,
            'category' => MediaCategory::EVENT,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
        ]);
        $user->media()->save($media);

        Event::factory()->create([
            'title' => 'Uploaded Image Event',
            'department_id' => null,
            'image' => 'https://example.com/legacy-event.jpg',
            'image_media_id' => $media->id,
            'status' => EventStatus::UPCOMING->value,
        ]);

        $response = $this->get(route('events'));

        $response->assertOk();
        $response->assertSee($media->file_path, false);
        $response->assertDontSee('https://example.com/legacy-event.jpg', false);
    }

    public function test_events_page_keeps_legacy_and_placeholder_image_fallbacks(): void
    {
        Event::factory()->create([
            'title' => 'Legacy Image Event',
            'department_id' => null,
            'image' => 'https://example.com/legacy-event.jpg',
            'status' => EventStatus::UPCOMING->value,
        ]);
        Event::factory()->create([
            'title' => 'No Image Event',
            'department_id' => null,
            'image' => null,
            'image_media_id' => null,
            'status' => EventStatus::UPCOMING->value,
        ]);

        $response = $this->get(route('events'));

        $response->assertOk();
        $response->assertSee('https://example.com/legacy-event.jpg', false);
        $response->assertSee(asset('assets/images/resources/events-img-1.jpg'), false);
    }

    public function test_creating_event_from_confirmed_crop_links_uploaded_image(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $uploadedUrl = 'https://res.cloudinary.com/example/image/upload/events/created-event.jpg';

        $this->mock(CroppedImageUploadService::class, function ($mock) use ($uploadedUrl) {
            $mock->shouldReceive('storeFromDataUrl')
                ->once()
                ->andReturn([
                    'url' => $uploadedUrl,
                    'original_name' => 'created-event.jpg',
                    'size' => 100,
                ]);
        });

        $response = $this->actingAs($user)->post(route('dashboard.events.store'), [
            'title' => 'Created Image Event',
            'event_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'location' => 'Main Auditorium',
            'department_id' => null,
            'status' => EventStatus::UPCOMING->value,
            'image_cropped' => 'data:image/jpeg;base64,ZmFrZQ==',
        ]);

        $response->assertRedirect(route('dashboard.events.index'));

        $event = Event::query()->where('title', 'Created Image Event')->firstOrFail();
        $this->assertSame($uploadedUrl, $event->image);
        $this->assertNotNull($event->image_media_id);
        $this->assertSame($uploadedUrl, $event->image_url);
    }

    public function test_sermon_public_pages_use_current_uploaded_cover_image(): void
    {
        $speaker = User::factory()->create(['role' => UserRole::PASTOR]);
        $mediaOwner = User::factory()->create(['role' => UserRole::ADMIN]);
        $media = new Media([
            'title' => 'Current Sermon Cover',
            'file_name' => 'current-sermon-cover.jpg',
            'file_path' => 'https://res.cloudinary.com/example/image/upload/sermons/current-cover.jpg',
            'size' => 100,
            'media_type' => MediaType::IMAGE,
            'category' => MediaCategory::SERMON,
            'is_public' => true,
            'upload_status' => MediaUploadStatus::READY,
        ]);
        $mediaOwner->media()->save($media);
        $sermon = Sermon::factory()->create([
            'speaker_id' => $speaker->id,
            'status' => SermonStatus::PUBLISHED->value,
            'video_url' => 'https://www.youtube.com/watch?v=abc123xyz89',
            'audio_url' => 'https://example.com/sermon.mp3',
            'cover_image' => 'https://example.com/old-sermon-cover.jpg',
            'cover_media_id' => $media->id,
        ]);

        foreach ([route('home'), route('sermons'), route('sermons.show', $sermon->slug)] as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSee($media->file_path, false);
            $response->assertDontSee('https://example.com/old-sermon-cover.jpg', false);
        }
    }

    public function test_creating_sermon_from_confirmed_crop_saves_uploaded_cover_image(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $speaker = User::factory()->create(['role' => UserRole::PASTOR]);
        $uploadedUrl = 'https://res.cloudinary.com/example/image/upload/sermons/created-cover.jpg';

        $this->mock(CroppedImageUploadService::class, function ($mock) use ($uploadedUrl) {
            $mock->shouldReceive('storeFromDataUrl')
                ->once()
                ->andReturn([
                    'url' => $uploadedUrl,
                    'original_name' => 'created-cover.jpg',
                    'size' => 100,
                ]);
        });

        $response = $this->actingAs($admin)->post(route('dashboard.sermons.store'), [
            'title' => 'Created Cover Sermon',
            'description' => 'A sermon with an uploaded cover.',
            'sermon_date' => now()->format('Y-m-d'),
            'speaker_id' => $speaker->id,
            'status' => SermonStatus::PUBLISHED->value,
            'video_url' => 'https://www.youtube.com/watch?v=abc123xyz89',
            'cover_image_cropped' => 'data:image/jpeg;base64,ZmFrZQ==',
        ]);

        $response->assertRedirect(route('dashboard.sermons.index'));

        $sermon = Sermon::query()->where('title', 'Created Cover Sermon')->firstOrFail();
        $this->assertSame($uploadedUrl, $sermon->cover_image);
        $this->assertNull($sermon->cover_media_id);
        $this->assertSame($uploadedUrl, $sermon->cover_image_url);
    }
}
