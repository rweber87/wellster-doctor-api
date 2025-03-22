<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email'];

    public function indications()
    {
        return $this->belongsToMany(Indication::class, 'doctor_indications');
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function patients()
    {
        return $this->hasManyThrough(Patient::class, Assignment::class, 'doctor_id', 'id', 'id', 'patient_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
