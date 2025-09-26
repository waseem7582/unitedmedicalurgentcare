<?php
namespace App\Constants;

use App\Models\Hospital\HospitalWallet;
use Illuminate\Support\Str;

class PaymentGatewayConst {

    const ACTIVE                    =  true;

    const AUTOMATIC                 = "AUTOMATIC";
    const MANUAL                    = "MANUAL";
    const PAYMENTMETHOD             = "payment-method";
    const MONEYOUT                  = "Money Out";
    const TYPEWITHDRAW              = "WITHDRAW";

    const TYPEMONEYOUT              = "MONEY-OUT";
    const TYPEADDSUBTRACTBALANCE    = "ADD-SUBTRACT-BALANCE";

    const ENV_SANDBOX               = "SANDBOX";
    const ENV_PRODUCTION            = "PRODUCTION";

    const APP                       = "APP";

    const STATUS_SUCCESS             = 1;
    const STATUS_PENDING             = 2;
    const STATUS_HOLD                = 3;
    const STATUS_REJECTED            = 4;
    const STATUS_WAITING             = 5;


    const PAYPAL                    = 'paypal';
    const G_PAY                     = 'gpay';
    const COIN_GATE                 = 'coingate';
    const QRPAY                     = 'qrpay';
    const STRIPE                    = 'stripe';
    const FLUTTERWAVE               = 'flutterwave';
    const TATUM                     = 'tatum';
    const SSLCOMMERZ                = 'sslcommerz';
    const RAZORPAY                  = 'razorpay';
    const PERFECT_MONEY             = 'perfect-money';
    const PAYSTACK                  = "paystack"; 
    const AUTHORIZE                 = "authorize";


    const SEND                      = "SEND";
    const RECEIVED                  = "RECEIVED";
    const PENDING                   = "PENDING";
    const REJECTED                  = "REJECTED";
    const CREATED                   = "CREATED";
    const SUCCESS                   = "SUCCESS";
    const EXPIRED                   = "EXPIRED";

    const FIAT                      = "FIAT";
    const CRYPTO                    = "CRYPTO";
    const CRYPTO_NATIVE             = "CRYPTO_NATIVE";

    const PROJECT_CURRENCY_SINGLE   = "PROJECT_CURRENCY_SINGLE";


    const CALLBACK_HANDLE_INTERNAL  = "CALLBACK_HANDLE_INTERNAL";

    const NOT_USED  = "NOT-USED";
    const USED      = "USED";
    const SENT      = "SENT";

    const REDIRECT_USING_HTML_FORM = "REDIRECT_USING_HTML_FORM";

    public static function payment_gateway_slug() {
        return Str::slug(self::PAYMENTMETHOD);
    }

    public static function money_out_slug() {
        return Str::slug(self::MONEYOUT);
    }


    public static function register($alias = null) {
        $gateway_alias  = [
            self::PAYPAL        => "paypalInit",
            self::COIN_GATE     => "coinGateInit",
            self::QRPAY         => "qrpayInit",
            self::STRIPE        => 'stripeInit',
            self::TATUM         => 'tatumInit',
            self::FLUTTERWAVE   => 'flutterwaveInit',
            self::SSLCOMMERZ    => 'sslCommerzInit',
            self::RAZORPAY      => 'razorpayInit',
            self::PERFECT_MONEY => 'perfectMoneyInit',
            self::PAYSTACK      => 'paystackInit',
            self::AUTHORIZE     => 'authorizeInit'
        ];

        if($alias == null) {
            return $gateway_alias;
        }

        if(array_key_exists($alias,$gateway_alias)) {
            return $gateway_alias[$alias];
        }
        return "init";
    }



    public static function apiAuthenticateGuard() {
        return [
            'api'   => 'web',
            'hospital_api'   => 'hospital',
        ];
    }

    public static function registerRedirection() {
        return [
            'web'       => [
                'return_url'    => 'frontend.doctor.booking.payment.success',
                'cancel_url'    => 'frontend.doctor.booking.payment.cancel',
                'callback_url'  => 'frontend.doctor.booking.payment.callback',
                'redirect_form' => 'frontend.doctor.booking.payment.redirect.form',
                'btn_pay'       => 'frontend.doctor.booking.payment.btn.pay',
            ],
            'api'       => [
                'return_url'    => 'api.user.payment.success',
                'cancel_url'    => 'api.user.payment.cancel',
                'callback_url'  => 'frontend.doctor.booking.payment.callback',
                'redirect_form' => 'frontend.doctor.booking.payment.redirect.form',
                'btn_pay'       => 'api.user.payment.btn.pay',
            ],
        ];
    }


    public static function hospitalRegisterRedirection() {
        return [
            'hospital'       => [
                'return_url'    => 'hospitals.admin.charges.payment.success',
                'cancel_url'    => 'hospitals.admin.charges.payment.cancel',
                'callback_url'  => 'hospitals.admin.charges.payment.callback',
                'redirect_form' => 'hospitals.admin.charges.payment.redirect.form',
                'btn_pay'       => 'hospitals.admin.charges.payment.btn.pay',
            ],
            'api'       => [
                'return_url'    => 'api.v1.hospitals.add.money.payment.success',
                'cancel_url'    => 'api.v1.hospitals.add.money.payment.cancel',
                'callback_url'  => 'api.v1.hospitals.add.money.payment.callback',
                'redirect_form' => 'api.v1.hospitals.add.money.payment.redirect.form',
                'btn_pay'       => 'api.v1.hospitals.add.money.payment.btn.pay',
            ],
        ];
    }

    public static function registerGatewayRecognization() {
        return [
            'isPaypal'          => self::PAYPAL,
            'isCoinGate'        => self::COIN_GATE,
            'isQrpay'           => self::QRPAY,
            'isStripe'          => self::STRIPE,
            'isTatum'           => self::TATUM,
            'isFlutterwave'     => self::FLUTTERWAVE,
            'isSslCommerz'      => self::SSLCOMMERZ,
            'isRazorpay'        => self::RAZORPAY,
            'isPerfectMoney'    => self::PERFECT_MONEY,
            'isAuthorize'       => self::AUTHORIZE,
        ];
    }

    public static function payment_method_slug() {
        return Str::slug(self::PAYMENTMETHOD);
    }

    public static function registerWallet() {
        return [
            'api'               => HospitalWallet::class,
            'hospital'            => HospitalWallet::class,
            'hospital_api'        => HospitalWallet::class,
        ];
    }

    public static function hospitalRegisterWallet() {
        return [
            'api'            => HospitalWallet::class,
            'web'            => HospitalWallet::class,
        ];
    }



}
