<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class UserService
{
    public function updateUser( $id, array $data)
    {
        $user=User::find($id);
        $user->update($data);
        return $user;
    }

    public function deleteUser($id)
    {
        $user=User::find($id);
        $user->delete();
    }
}
