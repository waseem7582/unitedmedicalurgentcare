<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalPasswordReset extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function hospital() {
        return $this->belongsTo(Hospital::class)->select('id','username','email','hospital_name');
    }

    protected $casts = [
        'id'        => 'integer',
        'email'     => 'string',
        'code'      => 'integer',
        'token'     => 'string',
        'hospital_id' => 'integer',
    ];
}
