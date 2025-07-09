<?php

namespace App\Services;

class UserService
{
    public function delete($user)
    {
        return $user->delete();
    }
}
