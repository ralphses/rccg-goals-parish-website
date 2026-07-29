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
}
