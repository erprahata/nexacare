<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MedicalRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'appointment_id', 'pre_triage_notes', 'diagnosis', 'doctor_notes', 'is_locked'
    ];

    protected $casts = [
        'pre_triage_notes' => 'array', // Otomatis mengubah JSON db menjadi Array PHP
        'is_locked' => 'boolean',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}