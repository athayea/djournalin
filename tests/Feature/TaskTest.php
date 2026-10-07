<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Siapkan Laporan Sprint Review',
            'desc' => 'Rekap velocity backlog.',
            'priority' => 'high',
            'category' => 'Pekerjaan',
            'due' => '2026-10-06 16:00',
        ], $overrides);
    }

    public function test_guests_get_demo_tasks_that_are_not_saved(): void
    {
        $this->get('/tasks')
            ->assertOk()
            ->assertViewHas('journalinData', fn (array $data) => $data['isAuthenticated'] === false && count($data['tasks']) === 4);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_guests_cannot_use_the_task_endpoints(): void
    {
        $this->postJson('/tasks', $this->validPayload())->assertUnauthorized();
        $this->putJson('/tasks/1', $this->validPayload())->assertUnauthorized();
        $this->patchJson('/tasks/1/toggle')->assertUnauthorized();
        $this->deleteJson('/tasks/1')->assertUnauthorized();

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_a_logged_in_user_only_sees_their_own_task_history(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['title' => 'Tugas milik saya']);
        Task::factory()->create(['title' => 'Tugas orang lain']);

        $this->actingAs($user)->get('/tasks')
            ->assertOk()
            ->assertViewHas('journalinData', fn (array $data) => $data['isAuthenticated'] === true
                && collect($data['tasks'])->pluck('title')->all() === ['Tugas milik saya']);
    }

    public function test_a_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/tasks', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('title', 'Siapkan Laporan Sprint Review')
            ->assertJsonPath('due', '2026-10-06 16:00')
            ->assertJsonPath('done', false);

        $this->assertDatabaseHas('tasks', ['user_id' => $user->id, 'priority' => 'high', 'category' => 'Pekerjaan']);
    }

    public function test_a_task_needs_valid_input(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/tasks', $this->validPayload(['title' => '', 'priority' => 'urgent', 'category' => 'Lain', 'due' => 'besok']))
            ->assertJsonValidationErrors(['title', 'priority', 'category', 'due']);
    }

    public function test_a_user_can_update_a_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)->putJson("/tasks/{$task->id}", $this->validPayload(['title' => 'Judul baru', 'priority' => 'low']))
            ->assertOk()
            ->assertJsonPath('title', 'Judul baru');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Judul baru', 'priority' => 'low']);
    }

    public function test_a_user_can_check_and_uncheck_a_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)->patchJson("/tasks/{$task->id}/toggle")->assertOk()->assertJsonPath('done', true);
        $this->assertNotNull($task->fresh()->completed_at);

        $this->actingAs($user)->patchJson("/tasks/{$task->id}/toggle")->assertOk()->assertJsonPath('done', false);
        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_a_user_can_delete_a_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)->deleteJson("/tasks/{$task->id}")->assertNoContent();

        $this->assertModelMissing($task);
    }

    public function test_a_user_cannot_touch_another_users_task(): void
    {
        $task = Task::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->putJson("/tasks/{$task->id}", $this->validPayload())->assertNotFound();
        $this->actingAs($intruder)->patchJson("/tasks/{$task->id}/toggle")->assertNotFound();
        $this->actingAs($intruder)->deleteJson("/tasks/{$task->id}")->assertNotFound();

        $this->assertModelExists($task);
    }

    public function test_the_seeder_creates_the_dummy_accounts_with_sample_history(): void
    {
        $this->seed();

        $citra = User::where('username', 'citra')->firstOrFail();
        $this->assertSame(4, $citra->tasks()->count());
        $this->assertSame(1, $citra->tasks()->whereNotNull('completed_at')->count());
        $this->assertDatabaseHas('users', ['username' => 'demo']);
        $this->post('/login', ['username' => 'demo', 'password' => 'password123'])->assertRedirect(route('dashboard'));
    }
}
