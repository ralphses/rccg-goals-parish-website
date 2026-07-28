<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_cannot_access_media_dashboard(): void
    {
        // Default role is MEMBER
        $user = User::factory()->create([
            'role' => UserRole::MEMBER,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/media');

        $response->assertStatus(403);
    }

    public function test_authorized_roles_can_access_media_dashboard(): void
    {
        foreach ([UserRole::ADMIN, UserRole::PASTOR, UserRole::MEDIA, UserRole::EDITOR] as $role) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $response = $this->actingAs($user)->get('/dashboard/media');

            $response->assertStatus(200);
        }
    }
}
