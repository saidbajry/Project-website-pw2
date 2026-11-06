<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    protected $guarded = [];
    protected $casts = ['booking_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function vehicle() { return $this->belongsTo(GarageVehicle::class, 'garage_vehicle_id'); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function workOrder() { return $this->hasOne(WorkOrder::class); }
}
