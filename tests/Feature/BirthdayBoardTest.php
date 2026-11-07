<?php

namespace Tests\Feature;

use App\enums\MediaCategory;
use App\enums\UserRole;
use App\enums\UserStatus;
use App\Models\BirthdayBoardComment;
use App\Models\BirthdayGreeting;
use App\Models\Department;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirthdayBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_birthday_page_shows_only_current_published_cards(): void
    {
        $visible = User::factory()->create(['name' => 'Today Celebrant', 'birthday' => now('Africa/Lagos')->setYear(1992)]);
        $notToday = User::factory()->create(['name' => 'Tomorrow Celebrant', 'birthday' => now('Africa/Lagos')->addDay()->setYear(1992)]);
        $suspended = User::factory()->create(['name' => 'Suspended Celebrant', 'birthday' => now('Africa/Lagos')->setYear(1992), 'status' => UserStatus::SUSPENDED]);

        $this->publicGreetingFor($visible);
        $this->publicGreetingFor($notToday);
        $this->publicGreetingFor($suspended);
        $this->publicGreetingFor(User::factory()->create(['name' => 'Old Card', 'birthday' => now('Africa/Lagos')->setYear(1992)]), now('Africa/Lagos')->subDay());
        BirthdayBoardComment::create([
            'user_id' => $visible->id,
            'celebration_date' => now('Africa/Lagos')->subDay()->toDateString(),
            'message' => 'Yesterday\'s birthday wish.',
        ]);

        $this->get(route('birthdays'))
            ->assertOk()
            ->assertDontSee('page-header', false)
            ->assertSee('Today Celebrant')
            ->assertDontSee('Tomorrow Celebrant')
            ->assertDontSee('Suspended Celebrant')
            ->assertDontSee('Old Card')
            ->assertDontSee("Yesterday's birthday wish.");
    }

    public function test_public_birthday_page_shows_an_empty_state_when_no_card_is_published_today(): void
    {
        $this->get(route('birthdays'))
            ->assertOk()
            ->assertSee('No published birthday cards today');
    }

    public function test_every_active_church_role_can_post_a_live_daily_birthday_wish(): void
    {
        $this->publicGreetingFor(User::factory()->create(['birthday' => now('Africa/Lagos')->setYear(1992)]));

        foreach (UserRole::cases() as $role) {
            $user = User::factory()->create(['role' => $role, 'status' => UserStatus::ACTIVE]);
            $message = 'Happy birthday from '.$role->value.'!';

            $this->actingAs($user)
                ->post(route('birthdays.comments.store'), ['message' => $message])
                ->assertRedirect(route('birthdays'));

            $this->assertTrue(BirthdayBoardComment::query()
                ->where('user_id', $user->id)
                ->whereDate('celebration_date', now('Africa/Lagos'))
                ->where('message', $message)
                ->exists());
        }
    }

    public function test_guests_and_suspended_users_cannot_post_birthday_wishes(): void
    {
        $this->publicGreetingFor(User::factory()->create(['birthday' => now('Africa/Lagos')->setYear(1992)]));

        $this->post(route('birthdays.comments.store'), ['message' => 'Happy birthday!'])->assertRedirect(route('login'));

        $suspendedMember = User::factory()->create(['role' => UserRole::MEMBER, 'status' => UserStatus::SUSPENDED]);
        $this->actingAs($suspendedMember)
            ->post(route('birthdays.comments.store'), ['message' => 'Happy birthday!'])
            ->assertForbidden();
    }

    public function test_comment_validation_and_current_date_rules_are_enforced(): void
    {
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $this->actingAs($member)
            ->post(route('birthdays.comments.store'), ['message' => 'A message without a published card'])
            ->assertStatus(422);

        $this->publicGreetingFor(User::factory()->create(['birthday' => now('Africa/Lagos')->setYear(1992)]));
        $this->actingAs($member)
            ->post(route('birthdays.comments.store'), ['message' => ''])
            ->assertSessionHasErrors('message');
    }

    public function test_comment_author_and_birthday_studio_staff_can_remove_comments(): void
    {
        $author = User::factory()->create(['role' => UserRole::MEMBER]);
        $comment = BirthdayBoardComment::create([
            'user_id' => $author->id,
            'celebration_date' => now('Africa/Lagos')->toDateString(),
            'message' => 'A lovely birthday wish.',
        ]);

        $this->actingAs($author)
            ->delete(route('birthdays.comments.destroy', $comment))
            ->assertRedirect(route('birthdays'));
        $this->assertDatabaseMissing('birthday_board_comments', ['id' => $comment->id]);

        $comment = BirthdayBoardComment::create([
            'user_id' => $author->id,
            'celebration_date' => now('Africa/Lagos')->toDateString(),
            'message' => 'Another wish.',
        ]);
        $head = User::factory()->create(['role' => UserRole::MEMBER]);
        Department::factory()->create(['leader_id' => $head->id, 'status' => UserStatus::ACTIVE]);

        $this->actingAs($head)
            ->delete(route('birthdays.comments.destroy', $comment))
            ->assertRedirect(route('birthdays'));
        $this->assertDatabaseMissing('birthday_board_comments', ['id' => $comment->id]);
    }

    public function test_regular_members_cannot_remove_someone_elses_comment(): void
    {
        $author = User::factory()->create(['role' => UserRole::MEMBER]);
        $otherMember = User::factory()->create(['role' => UserRole::MEMBER]);
        $comment = BirthdayBoardComment::create([
            'user_id' => $author->id,
            'celebration_date' => now('Africa/Lagos')->toDateString(),
            'message' => 'Another wish.',
        ]);

        $this->actingAs($otherMember)
            ->delete(route('birthdays.comments.destroy', $comment))
            ->assertForbidden();
    }

    public function test_public_board_shows_only_the_five_newest_messages_for_today(): void
    {
        $celebrant = User::factory()->create(['birthday' => now('Africa/Lagos')->setYear(1992)]);
        $this->publicGreetingFor($celebrant);
        $author = User::factory()->create(['role' => UserRole::MEMBER]);

        foreach (range(1, 6) as $number) {
            $comment = BirthdayBoardComment::create([
                'user_id' => $author->id,
                'celebration_date' => now('Africa/Lagos')->toDateString(),
                'message' => 'Birthday message '.$number,
            ]);
            $comment->forceFill(['created_at' => now()->subMinutes(7 - $number)])->saveQuietly();
        }

        $this->get(route('birthdays'))
            ->assertOk()
            ->assertDontSee('Birthday message 1')
            ->assertSeeInOrder([
                'Birthday message 6',
                'Birthday message 5',
                'Birthday message 4',
                'Birthday message 3',
                'Birthday message 2',
            ]);
    }

    private function publicGreetingFor(User $user, $celebrationDate = null): BirthdayGreeting
    {
        $greeting = BirthdayGreeting::create([
            'user_id' => $user->id,
            'celebration_date' => $celebrationDate ?? now('Africa/Lagos')->toDateString(),
            'message' => 'Happy birthday!',
            'published_at' => now(),
        ]);
        $media = new Media([
            'title' => 'Birthday greeting',
            'file_name' => 'birthday-'.$user->id.'.jpg',
            'file_path' => 'birthdays/birthday-'.$user->id.'.jpg',
            'size' => 100,
            'media_type' => 'image',
            'category' => MediaCategory::BIRTHDAY,
            'is_public' => true,
        ]);
        $media->mediable()->associate($greeting);
        $media->save();
        $greeting->update(['media_id' => $media->id]);

        return $greeting;
    }
}
