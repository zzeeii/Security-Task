<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize()
    {
        
        return true; // Allow any authorized user to update task status
    }

    public function rules()
    {
        return [
            'status' => 'required|in:Open,In Progress,Completed,Blocked', // Status must be one of the allowed values
        ];
    }
}
