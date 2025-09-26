<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicSettings extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'mail_config'              => 'object',
        'sms_config'               => 'object',
        'push_notification_config' => 'object',
        'broadcast_config'         => 'object',
        'site_logo_dark'           => 'string',
        'site_logo'                => 'string',
        'site_fav_dark'            => 'string',
        'site_fav'                 => 'string',
        'site_fav'                 => 'string',

        'hospital_site_logo_dark'           => 'string',
        'hospital_site_logo'                => 'string',
        'hospital_site_fav_dark'            => 'string',
        'hospital_site_fav'                 => 'string',
        'hospital_site_fav'                 => 'string',

        'email_verification'                => 'string',
        'email_notification'                => 'string',
        'hospital_kyc_verification'           => 'string',
        'hospital_email_verification'         => 'string',
        'hospital_email_notification'         => 'string',
        'agree_policy'              => 'integer',
        'user_registration'             => 'integer',
        'hospital_agree_policy'             => 'integer',
        'hospital_registration'             => 'integer',

    ];


    public function mailConfig() {}
}
