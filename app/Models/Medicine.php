<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = ['sku_code', 'name', 'stock', 'price'];

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}