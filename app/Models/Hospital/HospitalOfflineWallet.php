<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalOfflineWallet extends Model
{
    use HasFactory;

    protected $fillable = ['balance', 'status','hospital_id','created_at','updated_at'];

    protected $casts = [
        'id'                    => 'integer',
        'hospital_id'           => 'integer',
        'balance'               => 'double',
        'profit_balance'        => 'decimal:8',
        'status'                => 'boolean',
    ];

    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }
}
