<?php

namespace App\Models\Hospital;

use App\Models\User;
use App\Models\Admin\PaymentGateway;
use App\Constants\PaymentGatewayConst;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorBooking extends Model
{
    use HasFactory;

    protected $guarded  = ['id'];

    protected $casts                    = [
        'id'                            => 'integer',
        'doctor_id'                     => 'integer',
        'hospital_id'                   => 'integer',
        'schedule_id'                   => 'integer',
        'user_id'                       => 'integer',
        'booking_data'                  => 'object',
        'payment_gateway_currency_id'   => 'integer',
        'trx_id'                        => 'string',
        'booking_exp_seconds'           => 'integer',
        'date'                          => 'string',
        'payment_method'                => 'string',
        'type'                          => 'string',
        'reject_reason'                 => 'string',
        'payment_currency'              => 'string',
        'slug'                          => 'string',
        'total_charge'                  => 'decimal:8',
        'price'                         => 'decimal:8',
        'payable_price'                 => 'decimal:8',
        'gateway_payable_price'         => 'decimal:8',
        'message'                       => 'string',
        'remark'                        => 'string',
        'status'                        => 'integer',
        'reject_reason'                 =>'string'
    ];

    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }

    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }
    public function schedule(){
        return $this->belongsTo(DoctorHasSchedule::class,'schedule_id');
    }

    public function payment_gateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway_currency() {
        return $this->belongsTo(PaymentGatewayCurrency::class,'payment_gateway_currency_id');
    }

    public function getStringStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == PaymentGatewayConst::STATUS_SUCCESS) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Success",
            ];
        }else if($status == PaymentGatewayConst::STATUS_PENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == PaymentGatewayConst::STATUS_HOLD) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Hold",
            ];
        }else if($status == PaymentGatewayConst::STATUS_REJECTED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => "Rejected",
            ];
        }else if($status == PaymentGatewayConst::STATUS_WAITING) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => "Waiting",
            ];
        }

        return (object) $data;
    }

    public function scopeSearch($query,$data) {
        return $query->where("trx_id","like","%".$data."%")
                ->orWhere('payment_method',"like","%" . $data . "%")
                ->orderBy('id','desc');;
    }

}
