<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPart extends Model
{
    protected $guarded = [];

    public function part() { return $this->belongsTo(Part::class); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}
