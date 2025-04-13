<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
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
        $this->createCustomerUser('John Brown', 'johnbrown@customer.com');
        $this->createCustomerUser('Jane Smith', 'janesmith@customer.com');
        $this->createCustomerUser('Bob Marley', 'bobmarley@customer.com');
    }

    public function createAdminUser()
    {
        User::create([
            'name'      => 'Admin User',
            'email'     => 'admin@vogueify.com',
            'password'  => Hash::make('adminuser'),
            'role'      => 'admin',
        ])->roles()->sync(Role::where('name', RoleName::ADMIN->value)->first());
    }

    public function createStaffUser()
    {
        User::create([
            'name'      => 'Jane Doe',
            'email'     => 'janedoe@vogueify.com',
            'password'  => Hash::make('janedoe1234'),
            'role'      => 'staff'
        ])->roles()->sync(Role::where('name', RoleName::STAFF->value)->first());
    }

    public function createCustomerUser(string $name, string $email)
    {
        $user = User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make('password123'),
            'role'      => 'customer'
        ]);

        $user->roles()->sync([
            Role::where('name', RoleName::CUSTOMER->value)->first()->id
        ]);
    }
}
