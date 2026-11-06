<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function garage() { return $this->hasMany(GarageVehicle::class); }
    public function bookings() { return $this->hasMany(ServiceBooking::class); }
    public function prospects() { return $this->hasMany(Prospect::class); }
}
