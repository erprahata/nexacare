<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Wajib dipanggil

class Patient extends Model
{
    use HasUuids; // Aktifkan UUID

    protected $fillable = [
        'medical_record_number', 
        'name', 
        'nik', 
        'dob', 
        'phone', 
        'address', 
        'allergies', // <-- DITAMBAHKAN
        'is_vvip'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}