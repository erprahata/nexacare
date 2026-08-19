<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Prescription extends Model
{
    use HasUuids;

    protected $fillable = ['medical_record_id', 'status'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}