<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalAuthorization extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function hospital() {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    protected $casts = [
        'id'        => "integer",
        'hospital_id' => "integer",
        'code'      => "integer",
        'token'     => "string",
    ];

}
