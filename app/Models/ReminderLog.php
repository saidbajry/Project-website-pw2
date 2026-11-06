<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $guarded = [];

    public function vehicle() { return $this->belongsTo(GarageVehicle::class, 'garage_vehicle_id'); }
}
