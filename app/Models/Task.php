<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'priority', 'category', 'due_at', 'completed_at'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    public const PRIORITIES = ['high', 'mid', 'low'];

    public const CATEGORIES = ['Pekerjaan', 'Pribadi', 'Kuliah & Belajar'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convert the front-end task shape (title, desc, due, done, doneAt) to database attributes.
     *
     * @param  array<string, mixed>  $task
     * @return array<string, mixed>
     */
    public static function attributesFromClient(array $task): array
    {
        $isDone = (bool) ($task['done'] ?? false);

        return [
            'title' => $task['title'],
            'description' => $task['desc'] ?? null,
            'priority' => $task['priority'],
            'category' => $task['category'],
            'due_at' => $task['due'],
            'completed_at' => $isDone ? substr($task['due'], 0, 10).' '.($task['doneAt'] ?? '00:00') : null,
        ];
    }

    /**
     * The task in the shape used by public/js/*.js.
     *
     * @return array<string, mixed>
     */
    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'desc' => $this->description ?? '',
            'priority' => $this->priority,
            'category' => $this->category,
            'due' => $this->due_at->format('Y-m-d H:i'),
            'done' => $this->completed_at !== null,
            'doneAt' => $this->completed_at?->format('H:i'),
        ];
    }
}
