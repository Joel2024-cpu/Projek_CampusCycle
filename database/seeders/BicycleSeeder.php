<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bicycle;
use Carbon\Carbon;

class BicycleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $bicycles = []; // Array kosong untuk menampung 30 sepeda

        // 1. Definisikan 3 MEREK utama
        // (Deskripsi dan Tipe diambil dari file Anda, tapi link gambar diperbaiki)
        $brands = [
            [
                'kode_prefix' => 'PLY',
                'merk' => 'Polygon',
                'type' => 'Sierra (City Bike)',
                'description' => 'Sepeda kampus klasik dengan keranjang depan untuk menaruh tas laptop. Dilengkapi boncengan belakang.',
                'image' => 'https://th.bing.com/th/id/OIP.1ky_P9alf0RVCgaScsC0MwHaHa?w=209&h=209&c=7&r=0&o=7&cb=ucfimgc2&dpr=1.3&pid=1.7&rm=3', // Link asli (pendek)
            ],
            [
                'kode_prefix' => 'UNT',
                'merk' => 'United',
                'type' => 'Detroit (MTB)',
                'description' => 'Sepeda gunung dengan suspensi depan yang empuk. Cocok untuk jalanan kampus yang bergelombang atau paving block.',
                'image' => 'https://th.bing.com/th/id/OIP.qrgQ_lEKT90O03Z5U_bYbwHaDt?w=329&h=175&c=7&r=0&o=7&cb=ucfimgc2&dpr=1.3&pid=1.7&rm=3', // Link asli (pendek)
            ],
            [
                'kode_prefix' => 'ELM',
                'merk' => 'Element',
                'type' => 'Ecosmo (Lipat)',
                'description' => 'Sepeda lipat yang ringkas dan gesit. Mudah diparkir di area sempit perpustakaan atau kantin.',
                'image' => 'https://th.bing.com/th/id/OIP.3Cs9fdUPCAk8dhVyRO3oNgHaFu?w=229&h=180&c=7&r=0&o=7&cb=ucfimgc2&dpr=1.3&pid=1.7&rm=3', // Link asli (pendek)
            ],
        ];

        // 2. Looping: 10x untuk setiap merek (Membuat 30 stok)
        foreach ($brands as $brand) {
            for ($i = 1; $i <= 10; $i++) {

                // Tambahkan data sepeda ke array
                $bicycles[] = [
                    // Membuat kode unik: PLY-01, PLY-02, ... UNT-01, UNT-02, ...
                    'kode_sepeda' => $brand['kode_prefix'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'merk' => $brand['merk'],
                    'type' => $brand['type'],
                    'description' => $brand['description'],
                    'image' => $brand['image'],
                    'status' => 'available', // Semua langsung tersedia
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // 3. Masukkan 30 data sepeda sekaligus ke database
        Bicycle::insert($bicycles);
    }
}


