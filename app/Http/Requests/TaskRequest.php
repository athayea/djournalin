<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Only logged in users can reach the task routes (see the auth middleware in routes/web.php).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'desc' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'category' => ['required', Rule::in(Task::CATEGORIES)],
            'due' => ['required', 'date_format:Y-m-d H:i'],
        ];
    }

    /**
     * The validated input mapped to the tasks table columns.
     *
     * @return array<string, mixed>
     */
    public function taskAttributes(): array
    {
        $data = $this->validated();

        return [
            'title' => $data['title'],
            'description' => $data['desc'] ?? null,
            'priority' => $data['priority'],
            'category' => $data['category'],
            'due_at' => $data['due'],
        ];
    }
}
