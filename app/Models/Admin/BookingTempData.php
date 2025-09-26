<?php

namespace App\Models\Admin;

use App\Models\Hospital\Doctor;
use App\Models\Hospital\DoctorHasSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BookingTempData extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];
    protected $casts                    = [
        'id'                            => 'integer',
        'data'                          => 'object',

    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function schedule(){
        return $this->belongsTo(DoctorHasSchedule::class,'schedule_id');
    }
    public function doctor(){
        return $this->belongsTo(Doctor::class,'doctor_id');
    }
    public function payment_gateway(){
        return $this->belongsTo(PaymentGatewayCurrency::class,'payment_gateway_currency_id');
    }
}
