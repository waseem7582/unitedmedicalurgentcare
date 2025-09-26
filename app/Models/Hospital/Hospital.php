<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use App\Constants\GlobalConst;
use App\Models\UserPasswordReset;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Hospital extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends   = ['fullname','stringStatus','lastLogin','kycStringStatus'];
    protected $dates     = ['deleted_at'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ["id"];

    /**
     * The attributes that should be hidden for serialization.n m
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                  => 'integer',
        'hospital_name'       => 'string',
        'username'            => 'string',
        'email'               => 'string',
        'mobile'              => 'string',
        'mobile_code'         => 'string',
        'password'            => 'string',
        'full_mobile'         => 'string',
        'refferal_user_id'    => 'integer',
        'image'               => 'string',
        'status'              => 'integer',
        'email_verified'      => 'integer',
        'sms_verified'        => 'integer',
        'kyc_verified'        => 'integer',
        'ver_code'            => 'integer',
        'two_factor_verified' => 'integer',
        'two_factor_status'   => 'integer',
        'two_factor_secret'   => 'string',
        'device_id'           => 'string',
        'remember_token'      => 'string',
        'email_verified_at'   => 'datetime',
        'address'             => 'object',
    ];

    public function branch(){
        return $this->hasMany(Branch::class,'hospital_id');
    }

    public function investigations(){
        return $this->hasMany(Investigation::class,'hospital_id');
    }



    public function scopeSmsUnverified($query)
    {
        return $query->where('sms_verified', false);
    }

    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('email_verified', false);
    }

    public function scopeEmailVerified($query) {
        return $query->where("email_verified",true);
    }

    public function scopeKycVerified($query) {
        return $query->where("kyc_verified",GlobalConst::VERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->whereNot('kyc_verified',GlobalConst::VERIFIED);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', false);
    }

    public function wallets()
    {
        return $this->hasOne(HospitalWallet::class);
    }

    public function kyc()
    {
        return $this->hasOne(HospitalKycData::class);
    }

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getUserImageAttribute() {
        $image = $this->image;

        if($image == null) {
            return files_asset_path('profile-default');
        }else if(filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }else {
            return files_asset_path("user-profile") . "/" . $image;
        }
    }

    public function passwordResets() {
        return $this->hasMany(UserPasswordReset::class,"user_id");
    }

    public function scopeGetSocial($query,$credentials) {
        return $query->where("email",$credentials);
    }

    public function getStringStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == GlobalConst::ACTIVE) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => __("Active"),
            ];
        }else if($status == GlobalConst::BANNED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => __("Banned"),
            ];
        }
        return (object) $data;
    }

    public function getKycStringStatusAttribute() {
        $status = $this->kyc_verified;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == GlobalConst::APPROVED) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => __("Verified"),
            ];
        }else if($status == GlobalConst::PENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => __("Pending"),
            ];
        }else if($status == GlobalConst::REJECTED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => __("Rejected"),
            ];
        }else {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => __("Unverified"),
            ];
        }
        return (object) $data;
    }

    public function loginLogs(){
        return $this->hasMany(HospitalLoginLog::class);
    }

    public function getLastLoginAttribute() {
        if($this->loginLogs()->count() > 0) {
            return $this->loginLogs()->get()->last()->created_at->format("H:i A, d M Y");
        }

        return "N/A";
    }

    public function scopeSearch($query,$data) {
        return $query->where(function($q) use ($data) {
            $q->where("username","like","%".$data."%");
        })->orWhere("email","like","%".$data."%")->orWhere("full_mobile","like","%".$data."%");
    }

}
