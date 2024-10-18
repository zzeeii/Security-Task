<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * The TaskService instance.
     *
     * @var TaskService
     */
    protected $taskService;

    /**
     * TaskController constructor.
     *
     * @param TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Create a new task.
     *
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create task');
        $assignedUser = $request->assigned_to;
    $currentUser = auth()->user();

    // Prevent assigning task to self
    if ($assignedUser == $currentUser->id) {
        return response()->json(['message' => 'Admins cannot assign tasks to themselves.'], 403);
    }

    // Prevent assigning task to another admin
    $assignedToUser = User::findOrFail($assignedUser); // Find the user the task is being assigned to

    if ($assignedToUser->hasRole('Admin')) {
        return response()->json(['message' => 'Admins cannot assign tasks to other admins.'], 403);
    }

        $task = $this->taskService->createTask($request->validated());

        return response()->json($task, 201);
    }

    /**
     * Update the status of a task.
     *
     * @param int $id
     * @param UpdateTaskStatusRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus($id, UpdateTaskStatusRequest $request)
    {
        $this->authorize('update task status');
        $task = $this->taskService->updateTaskStatus($id, $request->status);

        return response()->json($task);
    }

    /**
     * Reassign the task to another user.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reassignTask($id, Request $request)
    {
        $this->authorize('reassign task to another user');
        $assignedUser = $request->assigned_to;
    $currentUser = auth()->user();

    // Prevent reassigning to self
    if ($assignedUser == $currentUser->id) {
        return response()->json(['message' => 'Admins cannot assign tasks to themselves.'], 403);
    }

    // Prevent reassigning to another admin
    $assignedToUser = User::findOrFail($assignedUser); // Find the user the task is being reassigned to

    if ($assignedToUser->hasRole('Admin')) {
        return response()->json(['message' => 'Admins cannot assign tasks to other admins.'], 403);
    }
        $task = $this->taskService->reassignTask($id, $request->assigned_to);

        return response()->json($task);
    }

    /**
     * Retrieve all tasks with advanced filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tasks = $this->taskService->getTasks($request->all());

        return response()->json($tasks);
    }

    /**
     * Add a comment to a task.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment($id, Request $request)
    {
        $this->authorize('add comment to task');
        $comment = $this->taskService->addCommentToTask($id, [
            'body' => $request->body,
            'user_id' => auth()->user()->id
        ]);

        return response()->json($comment);
    }

    /**
     * Add an attachment to a task.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAttachment($id, Request $request)
    {
        $this->authorize('add attachment to task');
        
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments');
            $attachment = $this->taskService->addAttachmentToTask($id, $filePath);

            return response()->json($attachment);
        }
        
        return response()->json(['message' => 'No file attached'], 400);
    }

    /**
     * Generate a daily report of tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDailyReport()
    {
        $tasks = $this->taskService->generateDailyReport();

        return response()->json($tasks);
    }

    /**
     * Get tasks with a "Blocked" status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBlockedTasks()
    {
        $tasks = $this->taskService->getBlockedTasks();

        return response()->json($tasks);
    }

 /**
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function show($id)
{
   
    $task = $this->taskService->getTaskWithRelations($id);

    // Check if the task is assigned to the authenticated user
    if ($task->assigned_to !== auth()->id()) {
        return response()->json(['message' => 'You do not have access to this task.'], 403);
    }

    return response()->json($task);
}
/**
 * Soft delete a task.
 *
 * This method marks the task as deleted (soft delete) but keeps the record in the database.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function delete($id)
{
    $this->authorize('delete task');
    // Perform soft delete
    $this->taskService->softDeleteTask($id);

    return response()->json(['message' => 'Task soft deleted successfully.']);
}

/**
 * Force delete a task.
 *
 * This method permanently deletes the task from the database.
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function forceDelete($id)
{
    $this->authorize('delete task');

    // Perform force delete
    $this->taskService->forceDeleteTask($id);

    return response()->json(['message' => 'Task permanently deleted successfully.']);
}
/**
 * Display a list of deleted (soft deleted) tasks.
 *
 * This method retrieves tasks that have been soft deleted.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function getDeletedTasks()
{
    $this->authorize('view deleted tasks');

    $deletedTasks = $this->taskService->getDeletedTasks();

    return response()->json($deletedTasks);
}

/**
 * Restore a soft deleted task.
 *
 * This method restores a task that has been soft deleted.
 *
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function restoreTask($id)
{
    $this->authorize('delete task');

    $this->taskService->restoreTask($id);

    return response()->json(['message' => 'Task restored successfully.']);
}

}
