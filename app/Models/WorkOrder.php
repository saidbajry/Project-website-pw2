<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    public const STATUSES = ['antri', 'dikerjakan', 'selesai', 'diambil'];

    protected $guarded = [];
    protected $casts = ['started_at' => 'datetime', 'finished_at' => 'datetime'];

    public function vehicle() { return $this->belongsTo(GarageVehicle::class, 'garage_vehicle_id'); }
    public function booking() { return $this->belongsTo(ServiceBooking::class, 'service_booking_id'); }
    public function mechanic() { return $this->belongsTo(User::class, 'mechanic_id'); }
    public function parts() { return $this->hasMany(WorkOrderPart::class); }

    public function getPartsTotalAttribute(): int
    {
        return (int) $this->parts->sum(fn ($p) => $p->qty * $p->price);
    }

    public function getTotalAttribute(): int { return $this->labor_cost + $this->parts_total; }
}
