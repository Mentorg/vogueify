<?php

namespace App\Services;

use App\Models\Country;
use App\Models\User;

class UserService
{
    public function getUsers($paginate = false)
    {
        return $paginate ? User::paginate(15, ['*'], 'users_page') : User::all();
    }

    public function delete($user)
    {
        return $user->delete();
    }

    public function getProfile()
    {
        return Country::all(['id', 'name', 'iso_code']);
    }

    public function updateFirstTimeLogin($user)
    {
        $user->update(['is_first_login' => false]);
    }
}
