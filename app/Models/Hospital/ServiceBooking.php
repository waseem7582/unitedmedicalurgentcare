<?php

namespace App\Models\Hospital;

use App\Models\User;
use App\Constants\PaymentGatewayConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $guarded  = ['id'];

    protected $casts                    = [
        'id'                            => 'integer',
        'hospital_id'                   => 'integer',
        'user_id'                       => 'integer',
        'booking_data'                  => 'object',
        'payment_gateway_currency_id'   => 'integer',
        'trx_id'                        => 'string',
        'booking_exp_seconds'           => 'integer',
        'date'                          => 'string',
        'payment_method'                => 'string',
        'type'                          => 'string',
        'payment_currency'              => 'string',
        'slug'                          => 'string',
        'price'                         => 'decimal:8',
        'gateway_payable_price'         => 'decimal:8',
        'message'                       => 'string',
        'remark'                        => 'string',
        'status'                        => 'integer',
    ];

    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function hospital() {
        return $this->belongsTo(Hospital::class);
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
}
