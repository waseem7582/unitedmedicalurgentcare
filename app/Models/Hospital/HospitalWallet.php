<?php

namespace App\Models\Hospital;

use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalWallet extends Model
{
    use HasFactory;
    protected $fillable = ['balance', 'status','hospital_id','currency_id','created_at','updated_at'];

    protected $casts = [
        'id'                    => 'integer',
        'hospital_id'           => 'integer',
        'currency_id'           => 'integer',
        'balance'               => 'double',
        'profit_balance'        => 'decimal:8',
        'status'                => 'boolean',
    ];

    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }

    public function scopeGetHospital($query, $hospital_id) {
        return $query->where('hospital_id',$hospital_id);
    }

    public function scopeAuthApi($query) {
        return $query->where('hospital_id',auth()->guard('hospital_api')->user()->id);
    }

    public function scopeActive($query) {
        return $query->where("status",true);
    }

    public function hospital() {
        return $this->belongsTo(Hospital::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function scopeSender($query) {
        return $query->whereHas('currency',function($q) {
            $q->where("sender",GlobalConst::ACTIVE);
        });
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

}
