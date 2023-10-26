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
}
