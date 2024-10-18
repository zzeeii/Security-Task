<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes
Route::post('login', [AuthController::class, 'login']); // Route for user login
Route::post('register', [AuthController::class, 'register']); // Route for user registration

// Grouped routes with JWT Authentication, CSRF, and rate limiting middleware
Route::middleware(['auth:api', 'throttle:100,1'])->group(function () {

    // Task routes
    Route::post('tasks', [TaskController::class, 'store']); // Route to create a new task
    Route::put('tasks/{id}/status', [TaskController::class, 'updateStatus']); // Route to update task status
    Route::put('tasks/{id}/reassign', [TaskController::class, 'reassignTask']); // Route to reassign a task
    Route::get('tasks', [TaskController::class, 'index']); // Route to get all tasks with filters
    Route::post('tasks/{id}/comments', [TaskController::class, 'addComment']); // Route to add a comment to a task
    Route::post('tasks/{id}/attachments', [TaskController::class, 'addAttachment']); // Route to add an attachment to a task
    Route::get('task/{id}', [TaskController::class, 'show']); // Route to get task details by ID
    Route::delete('task/{id}/delete', [TaskController::class, 'delete']);
    Route::delete('task/{id}/forceDelete', [TaskController::class, 'forceDelete']);
    Route::get('task/{id}/getDeletedTasks', [TaskController::class, 'getDeletedTasks']);
    Route::patch('task/{id}/restore', [TaskController::class, 'restoreTask']);

    // Report routes
    Route::get('reports/daily-tasks', [TaskController::class, 'generateDailyReport']); // Route to generate daily task reports

    // Route to get tasks with a specific status like "Blocked"
    Route::get('tasks/blocked', [TaskController::class, 'getBlockedTasks']);


    //User routes
    Route::get('users',[UserController::class,'index']);
    Route::put('update/{id}/user',[UserController::class,'update']);
    Route::post('logout', [AuthController::class, 'logout']);// Route to logout the authenticated user
    Route::delete('delete/{id}/user',[UserController::class, 'destroy']);
});


