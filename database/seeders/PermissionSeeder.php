<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    protected array $permissions = [
        [
            'name' => 'create-transactions',
            'display_name' => 'Create Transactions',
            'description' => 'Create transactions',
        ],
        [
            'name' => 'view-transactions',
            'display_name' => 'View Transactions',
            'description' => 'View Transactions',
        ],
        [
            'name' => 'record-payments',
            'display_name' => 'Record Payments',
            'description' => 'Record Payments',
        ],
        [
            'name' => 'generate-reports',
            'display_name' => 'Generate Reports',
            'description' => 'Generate Reports',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $key => $value) {
            Permission::updateOrCreate(
                ['name' => $value['name']],
                $value
            );
        }
    }
}
