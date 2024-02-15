<?php

namespace Database\Seeders;

use App\Models\RideStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RideStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RideStatus::factory()->create(['name' => 'assigned']);
        RideStatus::factory()->create(['name' => 'finished']);
    }
}
