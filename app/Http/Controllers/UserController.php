<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        
        $users = User::all();
        return response()->json($users);
      
    }

    public function store(Request $request)
    {
     
    }

    public function update(UserUpdateRequest $request,$id)
    {
        
      
        $updatedUser = $this->userService->updateUser($id, $request->validated());
        return response()->json($updatedUser);
    }

    public function destroy($id)
    {
       
        $this->userService->deleteUser($id);
        return response()->json(null, 204);
    }
}
