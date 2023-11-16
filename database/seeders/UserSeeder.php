<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'name' => 'Ali',
            'email' => 'ali.sayed11298@gmail.com',
            'password' => Hash::make('testtest')
        ]);

        $adminRole = Role::firstWhere('name', 'admin');
        $admin->addRole($adminRole);

        //--------------------------------------------------
        $customer = User::updateOrCreate([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'password' => Hash::make('testtest')
        ]);

        $customerRole = Role::firstWhere('name', 'customer');
        $customer->addRole($customerRole);
    }
}
