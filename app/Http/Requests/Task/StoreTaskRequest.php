<?php

namespace App\Http\Requests\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow any authorized user to create a task
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255', // Task title is required, must be a string with a max length of 255
            'description' => 'required|string', // Task description is required
            'type' => 'required|in:Bug,Feature,Improvement', // Type must be one of the allowed values
            'status' => 'required|in:Open,In Progress,Completed,Blocked', // Status must be one of the allowed values
            'priority' => 'required|in:Low,Medium,High', // Priority must be one of the allowed values
            'due_date' => 'required|date', // Due date is required and must be a valid date
            'assigned_to' => 'required|exists:users,id' // Task must be assigned to a valid user
        ];
    }
}
