<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function testCanListTasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get('/api/tasks');
        $response->assertStatus(200);
    }

    public function testCanStoreTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/tasks', ['title' => 'Test Task']);
        $response->assertStatus(201);
    }

    public function testCanUpdateTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/tasks/{$task->id}", ['title' => 'Updated Task Title']);
        $response->assertStatus(200);

        $task->refresh();
        $this->assertEquals('Updated Task Title', $task->title);
    }

    public function testCannotUpdateOthersTask()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user1->id]);

        $this->actingAs($user2, 'sanctum');

        $response = $this->putJson("/api/tasks/{$task->id}", ['title' => 'Updated Task Title']);
        $response->assertStatus(403);
    }

    public function testCanMarkTaskAsDone()
    {
        // Assuming there's no sub-tasks involved for simplicity.
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->patchJson("/api/tasks/{$task->id}/done");
        $response->assertStatus(200);

        $task->refresh();
        $this->assertEquals('done', $task->status);
    }

    public function testCanDeleteTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'todo']);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testCannotDeleteDoneTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'done']);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(400);
    }
}
