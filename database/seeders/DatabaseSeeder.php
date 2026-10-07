<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Support\TaskPayload;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Dummy accounts (password for both: password123):
     *   citra / Citra Pratama  - starts with the sample tasks
     *   demo  / Demo User      - starts empty
     */
    public function run(): void
    {
        $citra = User::updateOrCreate(['username' => 'citra'], [
            'name' => 'Citra Pratama',
            'email' => 'citra@taskflow.id',
            'password' => 'password123',
        ]);

        User::updateOrCreate(['username' => 'demo'], [
            'name' => 'Demo User',
            'email' => 'demo@taskflow.id',
            'password' => 'password123',
        ]);

        if ($citra->tasks()->doesntExist()) {
            foreach (TaskPayload::sampleTasks() as $task) {
                $citra->tasks()->create(Task::attributesFromClient($task));
            }
        }
    }
}
