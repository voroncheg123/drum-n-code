<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    protected TaskService $taskService;

    /**
     * Create a new TaskController instance.
     *
     * @param TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {
        $this->middleware('auth:sanctum');
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'priority', 'search', 'sort']);
            $tasks = $this->taskService->getTasksWithFilters($filters);
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created task in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
        ]);

        $task = $this->taskService->createTask($validated);

        return response()->json($task, 201);
    }

    /**
     * Update the specified task in storage.
     *
     * @param Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $taskId): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($taskId);

            if (!$task) {
                return response()->json(['error' => 'Task not found.'], 404);
            }

            if ($task->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $updated = $this->taskService->updateTask($task, $request->all());

            if ($updated) {
                return response()->json($task->fresh(), 200); // Return the updated task object.
            }

            return response()->json(['error' => 'Failed to update task.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mark the specified task as done.
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsDone(int $taskId): JsonResponse
    {
        try {
            $isMarked = $this->taskService->markTaskAsDone($taskId);

            if ($isMarked) {
                $task = $this->taskService->getTaskById($taskId);
                return response()->json($task, 200);
            }

            return response()->json(['error' => 'Unable to mark task as done.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified task from storage.
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $taskId): JsonResponse
    {
        try {
            $this->taskService->deleteTask($taskId);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
