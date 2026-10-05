<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_redirect_redirects_to_google_provider(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_login_creates_new_user_with_anggota_role(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-12345');
        $googleUser->shouldReceive('getName')->andReturn('Budi Santoso');
        $googleUser->shouldReceive('getNickname')->andReturn(null);
        $googleUser->shouldReceive('getEmail')->andReturn('budi.santoso@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar.jpg');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@example.com',
            'google_id' => 'google-id-12345',
            'name' => 'Budi Santoso',
            'role' => 'anggota',
        ]);

        $user = User::where('email', 'budi.santoso@example.com')->first();
        $this->assertTrue($user->isAnggota());
        $this->assertFalse($user->isAdmin());
    }

    public function test_google_login_links_to_existing_email_user_without_duplication(): void
    {
        $existingUser = User::factory()->create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'google_id' => null,
            'role' => 'anggota',
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-99999');
        $googleUser->shouldReceive('getName')->andReturn('Existing User');
        $googleUser->shouldReceive('getNickname')->andReturn(null);
        $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar2.jpg');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($existingUser);
        $response->assertRedirect(route('dashboard', absolute: false));

        // Ensure user count did not increase
        $this->assertEquals(1, User::where('email', 'existing@example.com')->count());

        $existingUser->refresh();
        $this->assertEquals('google-id-99999', $existingUser->google_id);
    }

    public function test_google_login_authenticates_already_connected_google_user(): void
    {
        $connectedUser = User::factory()->create([
            'name' => 'Connected User',
            'email' => 'connected@example.com',
            'google_id' => 'google-id-77777',
            'role' => 'anggota',
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-77777');
        $googleUser->shouldReceive('getName')->andReturn('Connected User');
        $googleUser->shouldReceive('getNickname')->andReturn(null);
        $googleUser->shouldReceive('getEmail')->andReturn('connected@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($connectedUser);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_google_login_handles_cancellation_or_error_gracefully(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new \Exception('The user cancelled the OAuth process.'));

        $response = $this->get(route('auth.google.callback'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_user_cannot_access_admin_routes(): void
    {
        $googleUser = User::factory()->create([
            'email' => 'google_member@example.com',
            'google_id' => 'google-id-11111',
            'role' => 'anggota',
        ]);

        $response = $this->actingAs($googleUser)->get(route('admin.books.index'));

        // Admin middleware returns 403 for non-admin
        $response->assertStatus(403);
    }
}
