<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUsers($paginate = false)
    {
        return $paginate ? User::paginate(15) : User::all();
    }

    public function delete($user)
    {
        return $user->delete();
    }
}
