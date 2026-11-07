<?php

namespace Tests\Feature;

use App\enums\MediaCategory;
use App\enums\UserRole;
use App\enums\UserStatus;
use App\Models\BirthdayGreeting;
use App\Models\Department;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BirthdayGreetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_media_user_and_department_head_can_open_the_birthday_studio(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $media = User::factory()->create(['role' => UserRole::MEDIA]);
        $head = User::factory()->create(['role' => UserRole::MEMBER]);
        Department::factory()->create(['leader_id' => $head->id, 'status' => UserStatus::ACTIVE]);

        $this->actingAs($admin)->get(route('dashboard.birthdays.index'))->assertOk();
        $this->actingAs($media)->get(route('dashboard.birthdays.index'))->assertOk();
        $this->actingAs($head)->get(route('dashboard.birthdays.index'))->assertOk();
    }

    public function test_regular_members_cannot_open_the_birthday_studio(): void
    {
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $this->actingAs($member)->get(route('dashboard.birthdays.index'))->assertForbidden();
    }

    public function test_leaders_of_inactive_departments_cannot_open_the_birthday_studio(): void
    {
        $formerHead = User::factory()->create(['role' => UserRole::MEMBER]);
        Department::factory()->create(['leader_id' => $formerHead->id, 'status' => UserStatus::SUSPENDED]);

        $this->actingAs($formerHead)->get(route('dashboard.birthdays.index'))->assertForbidden();
    }

    public function test_media_user_can_generate_publish_and_download_a_birthday_card(): void
    {
        Storage::fake('public');
        $mediaUser = User::factory()->create(['role' => UserRole::MEDIA]);
        $celebrant = User::factory()->create([
            'birthday' => now('Africa/Lagos')->setYear(1990),
            'status' => UserStatus::ACTIVE,
            'avatar' => null,
        ]);

        $this->actingAs($mediaUser)->post(route('dashboard.birthdays.generate', $celebrant), [
            'message' => 'May your new year be filled with joy and grace.',
        ])->assertRedirect(route('dashboard.birthdays.index'));

        $greeting = BirthdayGreeting::with('media')->firstOrFail();
        $this->assertSame($celebrant->id, $greeting->user_id);
        $this->assertSame('May your new year be filled with joy and grace.', $greeting->message);
        $this->assertSame(MediaCategory::BIRTHDAY, $greeting->media->category);
        $this->assertFalse($greeting->media->is_public);

        $this->actingAs($mediaUser)->patch(route('dashboard.birthdays.publish', $greeting))->assertSessionHasNoErrors();
        $this->assertNotNull($greeting->refresh()->published_at);
        $this->assertTrue($greeting->media->refresh()->is_public);

        $this->actingAs($mediaUser)->get(route('dashboard.birthdays.download', $greeting))->assertOk();
    }

    public function test_regenerating_a_card_updates_the_existing_daily_greeting(): void
    {
        Storage::fake('public');
        $mediaUser = User::factory()->create(['role' => UserRole::MEDIA]);
        $celebrant = User::factory()->create([
            'birthday' => now('Africa/Lagos')->setYear(1990),
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($mediaUser)->post(route('dashboard.birthdays.generate', $celebrant), [
            'message' => 'First birthday message.',
        ])->assertRedirect(route('dashboard.birthdays.index'));

        $this->actingAs($mediaUser)->post(route('dashboard.birthdays.generate', $celebrant), [
            'message' => 'Updated birthday message.',
        ])->assertRedirect(route('dashboard.birthdays.index'));

        $this->assertSame(1, BirthdayGreeting::where('user_id', $celebrant->id)->count());
        $this->assertSame('Updated birthday message.', BirthdayGreeting::first()->message);
    }

    public function test_home_page_shows_only_published_greetings_for_today(): void
    {
        $user = User::factory()->create(['birthday' => now('Africa/Lagos')->setYear(1991)]);
        $greeting = BirthdayGreeting::create([
            'user_id' => $user->id,
            'celebration_date' => now('Africa/Lagos')->toDateString(),
            'message' => 'Happy birthday!',
            'published_at' => now(),
        ]);
        $media = new Media([
            'title' => 'Birthday greeting',
            'file_name' => 'birthday.jpg',
            'file_path' => 'birthdays/birthday.jpg',
            'size' => 100,
            'media_type' => 'image',
            'category' => MediaCategory::BIRTHDAY,
            'is_public' => true,
        ]);
        $media->mediable()->associate($greeting);
        $media->save();
        $greeting->update(['media_id' => $media->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee("Today's Birthday Celebrants", false)
            ->assertSee('thm-owl__carousel', false)
            ->assertSee($user->name);
    }

    public function test_home_page_hides_a_published_card_when_the_member_birthday_is_not_today(): void
    {
        $user = User::factory()->create(['birthday' => now('Africa/Lagos')->addDay()->setYear(1991)]);
        $greeting = BirthdayGreeting::create([
            'user_id' => $user->id,
            'celebration_date' => now('Africa/Lagos')->toDateString(),
            'message' => 'Happy birthday!',
            'published_at' => now(),
        ]);
        $media = new Media([
            'title' => 'Out-of-date birthday greeting',
            'file_name' => 'birthday.jpg',
            'file_path' => 'birthdays/birthday.jpg',
            'size' => 100,
            'media_type' => 'image',
            'category' => MediaCategory::BIRTHDAY,
            'is_public' => true,
        ]);
        $media->mediable()->associate($greeting);
        $media->save();
        $greeting->update(['media_id' => $media->id]);

        $this->get(route('home'))->assertOk()->assertDontSee($user->name);
    }
}
