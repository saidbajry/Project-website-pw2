<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    public const STATUSES = ['baru', 'dihubungi', 'survei', 'deal', 'batal'];

    protected $guarded = [];

    /** Cicilan per bulan dengan bunga flat per tahun. */
    public static function hitung(int $price, int $dp, int $tenor): int
    {
        $pokok = max($price - $dp, 0);
        $total = $pokok * (1 + config('mejeng.kredit_rate') * $tenor / 12);
        return (int) round($total / max($tenor, 1));
    }

    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function user() { return $this->belongsTo(User::class); }
}
