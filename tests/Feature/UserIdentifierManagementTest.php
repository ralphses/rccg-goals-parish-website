<?php

namespace Tests\Feature;

use App\enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIdentifierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_save_a_phone_only_identifier_in_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/dashboard/settings', [
            'name' => $user->name,
            'phone' => '0803 000 0000',
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertNull($user->refresh()->email);
        $this->assertSame('+2348030000000', $user->phone);
    }

    public function test_admins_can_create_phone_only_accounts(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name' => 'Phone Only Member',
            'phone' => '0803 000 0000',
            'role' => UserRole::MEMBER->value,
            'status' => 'active',
            'can_login' => true,
        ]);

        $response->assertRedirect(route('dashboard.users.index'));
        $user = User::where('phone', '+2348030000000')->first();

        $this->assertNotNull($user);
        $this->assertNull($user->email);
        $this->assertTrue($user->hasVerifiedEmail());
    }
}
