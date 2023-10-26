<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Create a new TaskController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Task::all();
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

        $task = Task::create($validated + ['user_id' => auth()->id()]);

        return response()->json($task, 201);
    }

    /**
     * Update the specified task in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->update($request->all());

        return response()->json($task, 200);
    }

    /**
     * Mark the specified task as done.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsDone(Task $task)
    {
        // Check if all sub-tasks are done:
        if ($task->subTasks()->where('status', 'todo')->exists()) {
            return response()->json(['error' => 'All sub-tasks must be completed first'], 400);
        }

        $task->update(['status' => 'done']);

        return response()->json($task, 200);
    }

    /**
     * Remove the specified task from storage.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        if ($task->status === 'done') {
            return response()->json(['error' => 'Cannot delete completed tasks'], 400);
        }

        $task->delete();

        return response()->json(null, 204);
    }
}
