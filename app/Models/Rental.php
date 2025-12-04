<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bicycle_id',
        'package_id',
        'start_time',
        'end_time',
        'return_time',
        'total_cost',
        'denda',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'return_time' => 'datetime',
    ];

    protected $appends = ['formatted_denda', 'is_late', 'effective_status', 'denda_kerusakan', 'total_biaya'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bicycle()
    {
        return $this->belongsTo(Bicycle::class, 'bicycle_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'rental_id');
    }

    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInHours($this->end_time, false) . ' jam';
        }
        return '-';
    }

    public function calculateDendaKeterlambatan()
    {
        $waktuCek = $this->return_time ? $this->return_time->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $batasWaktu = $this->end_time->setTimezone('Asia/Jakarta');

        if ($waktuCek->lte($batasWaktu)) {
            return 0;
        }

        $menitTerlambat = abs($waktuCek->diffInMinutes($batasWaktu));

        $blokSepuluhMenit = ceil($menitTerlambat / 10);
        return $blokSepuluhMenit * 5000;
    }

    public function calculateDenda()
    {
        return $this->calculateDendaKeterlambatan();
    }

    public function getDendaKerusakanAttribute()
    {
        return 0; 
    }

    public function getTotalBiayaAttribute()
    {
        $biayaPaket = $this->package->harga ?? $this->total_cost;
        $totalDenda = $this->denda + $this->denda_kerusakan;
        return $biayaPaket + $totalDenda;
    }

    public function getEffectiveStatusAttribute()
    {
        if ($this->status === 'selesai' && $this->calculateDendaKeterlambatan() > 0) {
            return 'selesai_terlambat';
        }

        if ($this->status === 'berjalan' && now()->gt($this->end_time)) {
            return 'berjalan_terlambat';
        }

        return $this->status;
    }

    public function getFormattedDendaAttribute()
    {
        $totalDenda = $this->denda + $this->denda_kerusakan;
        return $totalDenda > 0 ? 'Rp ' . number_format($totalDenda, 0, ',', '.') : '-';
    }

    public function getIsLateAttribute()
    {
        if ($this->return_time) {
            return $this->return_time->gt($this->end_time);
        }
        return now()->gt($this->end_time) && $this->status === 'berjalan';
    }

    public function syncDenda()
    {
        $calculatedDenda = $this->calculateDendaKeterlambatan();
        if ($this->denda != $calculatedDenda) {
            if ($this->status !== 'selesai') {
                $this->update(['denda' => $calculatedDenda]);
            }
        }
        return $calculatedDenda;
    }

    public function markAsReturned($returnTime = null)
    {
        $returnTime = $returnTime ?: now();
        $isLate = $returnTime->gt($this->end_time);

        $denda = 0;
        if ($isLate) {
            $minutesLate = $returnTime->diffInMinutes(Carbon::parse($this->end_time));
            $blocksOfTenMinutes = ceil($minutesLate / 10);
            $denda = $blocksOfTenMinutes * 5000;
        }

        $this->update([
            'return_time' => $returnTime,
            'status' => 'selesai', 
            'denda' => $denda,
        ]);

        return $denda;
    }
}

