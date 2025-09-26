<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupKyc extends Model
{
    use HasFactory;

    protected $casts = [
        'id'    => "integer",
        'fields'    => "object",
        'slug'    => "string",
        'user_type'    => "string",
        'status' => "integer"
    ];
    protected $guarded = ['id'];

    public function scopeHospitalKyc($query) {
        return $query->where("user_type","VENDOR")->active();
    }

    public function scopeActive($query) {
        $query->where("status",true);
    }
}
