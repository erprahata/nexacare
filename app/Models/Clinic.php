<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = ['name', 'is_executive'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
