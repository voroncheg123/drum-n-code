<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fetch all users
        $users = User::all();

        // For each user, create some tasks
        foreach ($users as $user) {
            // Let's say you want to create 5 tasks for each user.
            Task::factory()->count(5)->create(['user_id' => $user->id]);

            // If you want to create sub-tasks for these tasks, you can do so.
            // For example, for each task, create 1-3 sub-tasks.
            $user->tasks->each(function ($task) {
                Task::factory()->count(rand(1, 3))->create(['parent_id' => $task->id, 'user_id' => $task->user_id]);
            });
        }
    }
}
