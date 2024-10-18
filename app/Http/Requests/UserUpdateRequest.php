<?php

namespace App\Http\Requests;


use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        
        return true;
        
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|unique:users,email,',
       
        ];
    }
}
