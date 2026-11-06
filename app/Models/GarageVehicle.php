<?php

namespace App\Models;

use App\Services\ServicePredictor;
use Illuminate\Database\Eloquent\Model;

class GarageVehicle extends Model
{
    protected $guarded = [];
    protected $casts = ['last_service_date' => 'date'];

    public function prediksi(): array { return ServicePredictor::predict($this); }

    public function user() { return $this->belongsTo(User::class); }
    public function bookings() { return $this->hasMany(ServiceBooking::class); }
    public function workOrders() { return $this->hasMany(WorkOrder::class); }
    public function reminders() { return $this->hasMany(ReminderLog::class); }
}
