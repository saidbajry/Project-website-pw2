<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $guarded = [];

    public function isLow(): bool { return $this->stock <= $this->min_stock; }

    /** Buat draf PO otomatis bila stok menyentuh batas minimum. */
    public function buatDrafPoJikaMenipis(): void
    {
        if (! $this->isLow()) return;
        if (PurchaseOrder::where('part_id', $this->id)->whereIn('status', ['draf', 'dikirim'])->exists()) return;
        PurchaseOrder::create(['part_id' => $this->id, 'qty' => max($this->min_stock * 3 - $this->stock, 1)]);
    }

    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
}
