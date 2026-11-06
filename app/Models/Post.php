<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];
    protected $casts = ['is_published' => 'boolean'];

    public function getPhotoAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }
}
