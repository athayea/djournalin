<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_log_in_with_a_username_and_password(): void
    {
        $user = User::factory()->create(['username' => 'citra', 'password' => 'password123']);

        $response = $this->post('/login', ['username' => 'citra', 'password' => 'password123']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_a_wrong_password(): void
    {
        User::factory()->create(['username' => 'citra', 'password' => 'password123']);

        $response = $this->from('/login')->post('/login', ['username' => 'citra', 'password' => 'wrong']);

        $response->assertRedirect('/login')->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_requires_both_fields(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['username', 'password']);
    }

    public function test_a_logged_in_user_sees_their_name_and_a_logout_button(): void
    {
        $user = User::factory()->create(['name' => 'Citra Pratama']);

        $this->actingAs($user)->get('/')
            ->assertSee('Citra Pratama')
            ->assertSee('Keluar')
            ->assertDontSee('class="side-login">Login</a>', false);
    }

    public function test_a_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect(route('dashboard'));

        $this->assertGuest();
    }

    public function test_a_logged_in_user_is_redirected_away_from_the_login_page(): void
    {
        $this->actingAs(User::factory()->create())->get('/login')->assertRedirect();
    }

    public function test_an_unregistered_username_is_asked_to_register_first(): void
    {
        $response = $this->from('/login')->post('/login', ['username' => 'ghost', 'password' => 'password123']);

        $response->assertRedirect('/login')->assertSessionHasErrors('unregistered');
        $this->assertGuest();
    }

    public function test_the_login_page_shows_a_register_prompt_for_unregistered_users(): void
    {
        $this->from('/login')->followingRedirects()
            ->post('/login', ['username' => 'ghost', 'password' => 'password123'])
            ->assertSee('Silakan daftar terlebih dahulu')
            ->assertSee(route('register'));
    }

    public function test_the_register_page_renders(): void
    {
        $this->get('/register')->assertOk()->assertSee('name="email"', false)->assertSee('name="username"', false);
    }

    public function test_a_guest_can_register_and_then_log_in(): void
    {
        $response = $this->post('/register', [
            'email' => 'citra@example.com',
            'username' => 'citra',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'))->assertSessionHas('status');
        $this->assertGuest();
        $this->assertDatabaseHas('users', ['email' => 'citra@example.com', 'username' => 'citra']);

        $this->post('/login', ['username' => 'citra', 'password' => 'password123'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_registration_rejects_duplicate_email_and_username(): void
    {
        User::factory()->create(['email' => 'citra@example.com', 'username' => 'citra']);

        $this->post('/register', [
            'email' => 'citra@example.com',
            'username' => 'citra',
            'password' => 'password123',
        ])->assertSessionHasErrors(['email', 'username']);
    }

    public function test_registration_requires_all_fields(): void
    {
        $this->post('/register', [])->assertSessionHasErrors(['email', 'username', 'password']);
    }
}
