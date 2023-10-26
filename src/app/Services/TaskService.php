<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Exception;
use Illuminate\Support\Collection;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getTaskById(int $taskId): ?Task
    {
        return $this->taskRepository->find($taskId);
    }

    public function getAllTasks(): Collection
    {
        return $this->taskRepository->getAllTasks();
    }

    public function createTask(array $data): Task
    {
        $data['user_id'] = auth()->id(); // Set the user_id
        return $this->taskRepository->create($data);
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $this->taskRepository->update($task, $data);
    }

    public function markTaskAsDone(int $taskId): bool
    {
        $task = $this->taskRepository->find($taskId);

        if (!$task) {
            throw new Exception('Task not found.');
        }

        if ($this->hasIncompleteSubTasks($task)) {
            throw new Exception('All sub-tasks must be completed first.');
        }

        return $this->taskRepository->update($task, ['status' => 'done']);
    }


    public function canDeleteTask(Task $task): bool
    {
        return $task->status !== 'done';
    }

    public function deleteTask(int $taskId): void
    {
        $task = $this->taskRepository->find($taskId);

        if (!$task) {
            throw new \Exception("Task not found.");
        }

        if ($task->status === 'done') {
            throw new \Exception("Cannot delete completed tasks");
        }

        $this->taskRepository->delete($task);
    }

    private function hasIncompleteSubTasks(Task $task): bool
    {
        return $this->taskRepository->hasIncompleteSubTasks($task);
    }
}
