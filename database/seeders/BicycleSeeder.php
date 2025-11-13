<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bicycle;

class BicycleSeeder extends Seeder
{
    public function run(): void
    {
        Bicycle::insert([
            [
                'kode_sepeda' => 'CC-001',
                'merk' => 'Polygon',
                'type' => 'Urban',
                'description' => 'Sepeda kampus siap pakai',
                'image' => 'images/pict.png', 
                'status' => 'available',
            ],
            [
                'kode_sepeda' => 'CC-002',
                'merk' => 'United',
                'type' => 'Mountain',
                'description' => 'Sepeda kuat untuk jalan menanjak',
                'image' => 'images/pict.png',
                'status' => 'available',
            ],
        ]);
    }
}

