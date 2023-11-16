<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * @var Role
         */
        $adminRole = Role::firstWhere('name', 'admin');
        $adminRole->syncPermissions([
            'create-transactions',
            'view-transactions',
            'record-payments',
            'generate-reports'
        ]);

        $customerRole = Role::firstWhere('name', 'customer');
        $customerRole->syncPermissions([
            'view-transactions',
        ]);
    }
}
