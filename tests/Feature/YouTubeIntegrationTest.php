<?php

namespace Tests\Feature;

use App\enums\UserRole;
use App\Models\User;
use App\Models\YouTubeIntegration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YouTubeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_connect_and_disconnect_shared_youtube_channel(): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'access_token' => 'google-access-token',
                'refresh_token' => 'google-refresh-token',
                'expires_in' => 3600,
            ]),
            'www.googleapis.com/youtube/v3/channels*' => Http::response([
                'items' => [[
                    'id' => 'UC12345',
                    'snippet' => [
                        'title' => 'RCCG Goals Parish',
                        'thumbnails' => [
                            'default' => ['url' => 'https://example.test/thumb.jpg'],
                        ],
                    ],
                ]],
            ]),
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['youtube_oauth_state' => 'expected-state'])
            ->get(route('settings.youtube.callback', [
                'state' => 'expected-state',
                'code' => 'oauth-code',
            ]));

        $response->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('you_tube_integrations', [
            'channel_id' => 'UC12345',
            'channel_title' => 'RCCG Goals Parish',
            'connected_by' => $admin->id,
        ]);

        $disconnectResponse = $this->actingAs($admin)
            ->delete(route('settings.youtube.disconnect'));

        $disconnectResponse->assertRedirect(route('settings.index'));
        $this->assertDatabaseCount('you_tube_integrations', 0);
    }
}
