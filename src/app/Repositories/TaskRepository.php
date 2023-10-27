<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Find a task by its ID.
     *
     * @param int $id
     * @return Task|null
     */
    public function find(int $id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Retrieve all tasks from the database.
     *
     * @return Collection A collection of Task models.
     */
    public function getAllTasks(): Collection
    {
        return Task::all();
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    /**
     * Check if a task has incomplete sub-tasks.
     *
     * @param Task $task
     * @return bool
     */
    public function hasIncompleteSubTasks(Task $task): bool
    {
        return $task->subTasks()->where('status', 'todo')->exists();
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function delete(Task $task): ?bool
    {
        return $task->delete();
    }

    /**
     * Retrieve all tasks.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Task::all();
    }

    /**
     * Retrieve all tasks with the provided filters.
     *
     * @param array $filters An associative array containing the filters to be applied.
     *                       Supported filters: 'status', 'priority', 'search', and 'sort'.
     *
     * @return Collection A collection of tasks matching the applied filters.
     */
    public function getAllTasksWithFilters(array $filters): Collection
    {
        // Start building the query.
        $query = Task::query();

        // Filter by 'status' if it's provided in the filters.
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by 'priority' if it's provided in the filters.
        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Perform a full-text search in 'title' and 'description' if 'search' is provided in the filters.
        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($subquery) use ($searchTerm) {
                $subquery->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Handle sorting based on the 'sort' filter.
        // Supports multiple fields for sorting (comma-separated) and optional direction (prefix with '-' for descending).
        if (isset($filters['sort'])) {
            $sortFields = explode(',', $filters['sort']);
            foreach ($sortFields as $field) {
                $direction = 'asc'; // Default sort direction.

                // If the field is prefixed with '-', change direction to 'desc' and modify the field name.
                if (strpos($field, '-') === 0) {
                    $direction = 'desc';
                    $field = substr($field, 1);
                }

                // Ensure the sort field is one of the supported fields.
                if (in_array($field, ['createdAt', 'completedAt', 'priority'])) {
                    $query->orderBy($field, $direction);
                }
            }
        }

        // Execute the query and return the results.
        return $query->get();
    }
}
