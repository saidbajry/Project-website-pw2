<?php

namespace App\Services;

use App\Models\GarageVehicle;
use Carbon\Carbon;

/**
 * Mesin Kalkulasi Prediksi: memperkirakan jadwal servis berikutnya
 * dari kilometer terakhir, rata-rata pemakaian per bulan, dan interval bulan.
 */
class ServicePredictor
{
    public static function predict(GarageVehicle $v): array
    {
        $today = Carbon::today();
        $kmPerDay = max($v->avg_km_per_month, 1) / 30;

        $targetKm = $v->last_service_km + $v->interval_km;
        $kmLeft = $targetKm - $v->current_km;
        $daysByKm = $kmLeft <= 0 ? 0 : (int) ceil($kmLeft / $kmPerDay);

        $base = $v->last_service_date ? Carbon::parse($v->last_service_date) : $today;
        $dateByTime = $base->copy()->addMonths($v->interval_months)->startOfDay();
        $daysByTime = (int) floor($today->diffInDays($dateByTime, false));

        $days = max(min($daysByKm, $daysByTime), 0);
        $status = $days <= 0 ? 'terlambat' : ($days <= 14 ? 'mendekati' : 'aman');

        $span = max($v->interval_km, 1);
        $progress = (int) max(0, min(100, round(($v->current_km - $v->last_service_km) / $span * 100)));

        return [
            'hari'     => $days,
            'tanggal'  => $today->copy()->addDays($days),
            'target_km'=> $targetKm,
            'sisa_km'  => max($kmLeft, 0),
            'status'   => $status,
            'progress' => $progress,
        ];
    }
}
