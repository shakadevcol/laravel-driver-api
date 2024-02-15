<?php

namespace Database\Seeders;

use App\Models\DriverInformation;
use App\Models\PaymentInformation;
use App\Models\RiderInformation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
        ->has(DriverInformation::factory()->count(1))
        ->create([
            'email' => 'driver@gmail.com',
            'password' => '123456',
            'role_id' => Role::DRIVER,
        ]);
       

        User::factory()
        ->has(RiderInformation::factory()->count(1))
        ->has(PaymentInformation::factory()->count(1))
        ->create([
            'email' => 'rider@gmail.com',
            'password' => '123456',
            'role_id' => Role::RIDER,
        ]);
              
       User::factory(10)
       ->has(DriverInformation::factory()->count(1))
       ->create(['role_id' => Role::DRIVER]);

       User::factory(10)
       ->has(RiderInformation::factory()->count(1))
       ->has(PaymentInformation::factory()->count(1))
       ->create(['role_id' => Role::RIDER]);
    }
}
