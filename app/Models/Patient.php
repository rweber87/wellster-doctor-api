<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email'];

    public function indications()
    {
        return $this->belongsToMany(Indication::class, 'patient_indications');
    }

    public function doctor()
    {
        return $this->hasOneThrough(Doctor::class, Assignment::class, 'patient_id', 'id', 'id', 'doctor_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
