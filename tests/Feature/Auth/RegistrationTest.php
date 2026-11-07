<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasVerifiedEmail());
        Notification::assertNothingSent();
    }

    public function test_new_users_can_register_with_a_nigerian_phone_number(): void
    {
        $response = $this->post('/register', [
            'name' => 'Phone User',
            'phone' => '0803 000 0000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $user = User::where('phone', '+2348030000000')->first();

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->email);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_registration_requires_an_email_or_phone_number(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Missing Identifier',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')->assertSessionHasErrors(['email', 'phone']);
    }

    public function test_new_users_can_register_with_both_identifiers(): void
    {
        $this->post('/register', [
            'name' => 'Both Identifiers',
            'email' => 'BOTH@EXAMPLE.COM',
            'phone' => '+234 803 000 0001',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'both@example.com',
            'phone' => '+2348030000001',
        ]);
    }

    public function test_registration_rejects_invalid_nigerian_phone_numbers(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Invalid Phone',
            'phone' => '12345',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')->assertSessionHasErrors('phone');
    }

    public function test_registration_rejects_equivalent_phone_number_formats(): void
    {
        User::factory()->create(['phone' => '+2348030000000']);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Duplicate Phone',
            'phone' => '0803 000 0000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')->assertSessionHasErrors('phone');
    }
}
