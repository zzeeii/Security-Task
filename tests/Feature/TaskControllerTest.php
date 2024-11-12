<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_task()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $token = Auth::login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/tasks', [
                             'title' => 'New Task',
                             'description' => 'Task description',
                             'type' => 'Feature',
                             'priority' => 'High',
                             'due_date' => '2024-12-31',
                             'assigned_to' => $admin->id + 1, // Ensure it's a non-admin user
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'title', 'description', 'type', 'priority']);
    }

    public function test_user_can_update_task_status()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $token = auth()->login($admin);

        $task = Task::factory()->create(['assigned_to' => $admin->id]);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->putJson("/api/tasks/{$task->id}/status", [
                             'status' => 'Completed',
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'Completed']);
    }

    public function test_user_cannot_assign_task_to_another_admin()
    {
        $admin1 = User::factory()->create(['role' => 'Admin']);
        $admin2 = User::factory()->create(['role' => 'Admin']);
        $token = auth()->login($admin1);

        $task = Task::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->putJson("/api/tasks/{$task->id}/reassign", [
                             'assigned_to' => $admin2->id,
                         ]);

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Admins cannot assign tasks to other admins.']);
    }
}
