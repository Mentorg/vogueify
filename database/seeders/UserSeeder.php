<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAdminUser();
        $this->createStaffUser();
        $this->createCustomerUser('John Brown', 'mr', 'johnbrown@customer.com');
        $this->createCustomerUser('Jane Smith', 'ms', 'janesmith@customer.com');
        $this->createCustomerUser('Bob Marley', 'mr', 'bobmarley@customer.com');
    }

    public function createAdminUser()
    {
        User::create([
            'name'      => 'Admin User',
            'title'     => 'mr',
            'email'     => 'admin@vogueify.com',
            'password'  => Hash::make('adminuser'),
            'email_verified_at' => Carbon::now(),
            'role'      => 'admin',
            'is_first_login' => false,
        ])->roles()->sync(Role::where('name', RoleName::ADMIN->value)->first());
    }

    public function createStaffUser()
    {
        User::create([
            'name'      => 'Jane Doe',
            'title'     => 'ms',
            'email'     => 'janedoe@vogueify.com',
            'password'  => Hash::make('janedoe1234'),
            'email_verified_at' => Carbon::now(),
            'role'      => 'staff',
            'is_first_login' => false,
        ])->roles()->sync(Role::where('name', RoleName::STAFF->value)->first());
    }

    public function createCustomerUser(string $name, String $title, string $email, ?bool $is_first_login = false)
    {
        $user = User::create([
            'name'      => $name,
            'title'     => $title,
            'email'     => $email,
            'password'  => Hash::make('password123'),
            'email_verified_at' => Carbon::now(),
            'role'      => 'customer',
            'is_first_login' => $is_first_login
        ]);

        $user->roles()->sync([
            Role::where('name', RoleName::CUSTOMER->value)->first()->id
        ]);
    }
}
