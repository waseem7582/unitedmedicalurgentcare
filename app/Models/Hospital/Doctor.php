<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'id'                 => 'integer',
        'hospital_id'        => 'integer',
        'branch_id'          => 'integer',
        'department_id'      => 'integer',
        'name'               => 'string',
        'image'              => 'string',
        'title'              => 'string',
        'qualification'      => 'string',
        'specialty'          => 'string',
        'language'           => 'string',
        'designation'        => 'string',
        'contact'            => 'string',
        'off_days'           => 'string',
        'floor_number'       => 'integer',
        'room_number'        => 'integer',
        'address'            => 'string',
        'fees'               => 'double:8',
        'slug'               => 'string',
        'status'             => 'integer',
        'created_at'         => 'date:Y-m-d',
        'updated_at'         => 'date:Y-m-d',
    ];


    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }

    public function schedules(){
        return $this->hasMany(DoctorHasSchedule::class,'doctor_id');
    }

    public function booking(){
        return $this->hasMany(DoctorBooking::class);
    }

}
