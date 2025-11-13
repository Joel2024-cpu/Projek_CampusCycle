<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'durasi_jam',
        'harga',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
