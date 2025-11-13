<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'status_bayar',
        'metode',
        'total',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id');
    }

    public function isLunas()
    {
        return $this->status_bayar === 'lunas';
    }

    public function isBelum()
    {
        return $this->status_bayar === 'belum';
    }
}
