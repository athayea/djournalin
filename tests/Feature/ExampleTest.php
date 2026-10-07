<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_url_renders_the_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertViewIs('dashboard')
            ->assertSee('href="'.route('login').'" class="side-login">Login</a>', false);
    }

    public function test_the_tasks_page_remains_available_at_its_own_url(): void
    {
        $response = $this->get('/tasks');

        $response->assertViewIs('tasks.index');
    }

    public function test_the_login_page_is_available_from_the_sidebar_link(): void
    {
        $response = $this->get('/login');

        $response->assertViewIs('login');
    }
}
