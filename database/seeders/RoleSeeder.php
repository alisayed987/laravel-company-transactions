<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{

    protected array $roles = [
        [
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Has the ability to Create transactions, View transactions, Record payments and Generate reports',
        ],
        [
            'name' => 'customer',
            'display_name' => 'Customer',
            'description' => 'Has the ability to View their transactions',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->roles as $key => $value) {
            Role::updateOrCreate(
                ['name' => $value['name']],
                $value
            );
        }
    }
}
