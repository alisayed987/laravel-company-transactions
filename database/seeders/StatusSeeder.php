<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    protected array $statuses = [
        'Paid',
        'Outstanding',
        'Overdue'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->statuses as $value) {
            Status::updateOrCreate(['name' => $value]);
        }
    }
}
