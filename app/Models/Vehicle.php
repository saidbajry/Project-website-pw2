<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $guarded = [];
    protected $casts = ['featured' => 'boolean'];

    public function getTitleAttribute(): string
    {
        return trim("{$this->brand} {$this->model} {$this->variant}");
    }

    public function getPhotoAttribute(): ?string
    {
        return $this->image ? asset($this->image) : ($this->image_url ?: null);
    }

    public function prospects() { return $this->hasMany(Prospect::class); }
}
