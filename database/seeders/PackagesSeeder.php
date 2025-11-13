<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackagesSeeder extends Seeder
{
    public function run(): void
    {
        Package::insert([
            [
                'nama_paket' => 'Paket A',
                'durasi_jam' => 2,
                'harga' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_paket' => 'Paket B',
                'durasi_jam' => 4,
                'harga' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
