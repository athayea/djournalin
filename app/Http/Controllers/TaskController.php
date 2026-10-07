<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Support\TaskPayload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        return view('tasks.index', [
            'journalinData' => TaskPayload::for($request->user()),
        ]);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = $request->user()->tasks()->create($request->taskAttributes());

        return response()->json($task->toClientArray(), 201);
    }

    public function update(TaskRequest $request, int $task): JsonResponse
    {
        $task = $request->user()->tasks()->findOrFail($task);
        $task->update($request->taskAttributes());

        return response()->json($task->toClientArray());
    }

    public function toggle(Request $request, int $task): JsonResponse
    {
        $task = $request->user()->tasks()->findOrFail($task);
        $task->update(['completed_at' => $task->completed_at ? null : now()]);

        return response()->json($task->toClientArray());
    }

    public function destroy(Request $request, int $task): Response
    {
        $request->user()->tasks()->findOrFail($task)->delete();

        return response()->noContent();
    }
}
