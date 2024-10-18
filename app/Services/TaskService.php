<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\TaskStatusUpdate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class TaskService
{
    /**
     * Create a new task with the given data.
     *
     * @param array $validatedData
     * @return Task
     */
    public function createTask(array $validatedData)
    {
        return Task::create($validatedData);
    }

    /**
     * Update the status of a task and log the status update.
     * If the task is completed, unblock dependent tasks.
     *
     * @param int $taskId
     * @param string $status
     * @return Task
     */
    public function updateTaskStatus($taskId, $status)
    {
        $task = Task::findOrFail($taskId);
        $oldStatus = $task->status;

        // Update task status
        $task->update(['status' => $status]);

        // Log the status update
        TaskStatusUpdate::create([
            'task_id' => $taskId,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'updated_by' => auth()->user()->id,
        ]);

        // If task is completed, unblock dependent tasks
        if ($status === 'Completed') {
            $this->autoUnblockDependentTasks($taskId);
        }

        return $task;
    }

    /**
     * Reassign the task to another user.
     *
     * @param int $taskId
     * @param int $assignedTo
     * @return Task
     */
    public function reassignTask($taskId, $assignedTo)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['assigned_to' => $assignedTo]);

        return $task;
    }

    /**
     * Get a list of tasks based on the provided filters.
     * Cached for performance optimization.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasks(array $filters)
    {
        $userId = auth()->id();

        // Cache the tasks to improve performance, cache for 60 minutes
        return Cache::remember('tasks_for_user_' . $userId, 60, function () use ($filters, $userId) {
            $query = Task::where('assigned_to', $userId) // Ensure tasks are assigned to the authenticated user
                         ->with(['comments', 'attachments']); // Include comments and attachments
    
            // Apply filters if they exist

            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (isset($filters['assigned_to'])) {
                $query->where('assigned_to', $filters['assigned_to']);
            }
            if (isset($filters['due_date'])) {
                $query->where('due_date', '<=', $filters['due_date']);
            }
            if (isset($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            return $query->get();
        });
    }

    /**
     * Add a comment to a specific task.
     *
     * @param int $taskId
     * @param array $commentData
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addCommentToTask($taskId, array $commentData)
    {
        $task = Task::findOrFail($taskId);
        return $task->comments()->create($commentData);
    }

    /**
     * Add an attachment to a specific task.
     *
     * @param int $taskId
     * @param string $filePath
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addAttachmentToTask($taskId, $filePath)
    {
        $task = Task::findOrFail($taskId);
        return $task->attachments()->create([
            'file_path' => $filePath,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Automatically unblock dependent tasks when the parent task is completed.
     *
     * @param int $taskId
     * @return void
     */
    public function autoUnblockDependentTasks($taskId)
    {
        $dependencies = TaskDependency::where('depends_on', $taskId)->get();

        foreach ($dependencies as $dependency) {
            $dependentTask = Task::find($dependency->task_id);
            if ($dependentTask && $dependentTask->status == 'Blocked') {
                $dependentTask->update(['status' => 'Open']);
            }
        }
    }

    /**
     * Generate a daily report of tasks created today.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function generateDailyReport()
    {
        $today = Carbon::today();
        return Task::whereDate('created_at', $today)->get();
    }

    /**
     * Get tasks that are currently blocked.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlockedTasks()
    {
        return Task::where('status', 'Blocked')->get();
    }

/**
 * Get a task by ID along with its comments and attachments.
 *
 * @param int $taskId
 * @return Task
 */
public function getTaskWithRelations($taskId)
{
    return Task::with(['comments', 'attachments'])->findOrFail($taskId);
}

/**
 * Get a task by ID.
 *
 * @param int $taskId
 * @return Task
 */
public function getTask($taskId)
{
    return Task::findOrFail($taskId);
}

/**
 * Soft delete a task.
 *
 * @param int $taskId
 * @return void
 */
public function softDeleteTask($taskId)
{
    $task = Task::findOrFail($taskId);
    $task->delete(); // Perform soft delete
}

/**
 * Force delete a task.
 *
 * @param int $taskId
 * @return void
 */
public function forceDeleteTask($taskId)
{
    $task = Task::withTrashed()->findOrFail($taskId);
    $task->forceDelete(); // Perform permanent delete
}
/**
 * Retrieve all soft deleted tasks.
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getDeletedTasks()
{
    return Task::onlyTrashed()->get(); // Retrieve only soft deleted tasks
}

/**
 * Retrieve a specific soft deleted task by ID.
 *
 * @param int $taskId
 * @return Task
 */
public function getDeletedTask($taskId)
{
    return Task::onlyTrashed()->findOrFail($taskId); // Find the task, even if soft deleted
}

/**
 * Restore a soft deleted task.
 *
 * @param int $taskId
 * @return void
 */
public function restoreTask($taskId)
{
    $task = Task::onlyTrashed()->findOrFail($taskId);
    $task->restore(); // Restore the soft deleted task
}


}
