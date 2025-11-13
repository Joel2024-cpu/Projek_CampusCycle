<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Bicycle;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin CampusCycle',
            'email' => 'admin@campuscycle.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        $this->call([
            BicycleSeeder::class,
            PackagesSeeder::class,
        ]);
    }
}
