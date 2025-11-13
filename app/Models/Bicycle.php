<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bicycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_sepeda',
        'merk',
        'type',
        'description',
        'image', 
        'status',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_RENTED = 'rented';
    const STATUS_MAINTENANCE = 'maintenance';

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'bicycle_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }
}

