<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\BasicSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Exception;

class BasicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'id' => '1',
            'site_name' => 'DoctorHub',
            'site_title' => 'Hospital, Doctor & Patient Booking Web + App',
            'base_color' => '#637DFE',
            'secondary_color' => '#203499',

            'hospital_site_name' => 'DoctorHub Hospital',
            'hospital_site_title' => 'Manage Hospital Branches, Doctors & Services',
            'hospital_base_color' => '#7A5FFF',
            'hospital_secondary_color' => '#552DD3',

            'otp_exp_seconds' => '3600',
            'timezone' => 'Asia/Dhaka',

            'user_registration' => '1',
            'secure_password' => '0',
            'agree_policy' => '0',
            'force_ssl' => '0',
            'email_verification' => '0',
            'sms_verification' => '0',
            'email_notification' => '0',
            'push_notification' => '1',
            'kyc_verification' => '0',

            'site_logo_dark' => '3a79216d-a052-4180-9d0a-30d3f75140cb.webp',
            'site_logo' => '5311b680-2f5c-4865-9d59-4170d99d2f27.webp',
            'site_fav_dark' => '3a7f764d-14ff-4868-83cf-7c001e9bcbaa.webp',
            'site_fav' => '9212c9c9-aa91-45d7-9f46-38d9e5acbe48.webp',

            'hospital_site_logo_dark' => '4b2b7237-548a-4586-a47a-c9815b172ebb.webp',
            'hospital_site_logo' => 'c5abae14-bb4c-4c2f-b00e-04431d9ebdab.webp',
            'hospital_site_fav_dark' => '1560af8d-d580-4c5d-9196-bf7e008d4e5e.webp',
            'hospital_site_fav' => 'a9c8538c-e73f-406c-9ac2-586a6ab4ef9a.webp',


            'preloader_image' => NULL,

            'mail_config' => [

                "method" => "smtp",
                   "host" => "appdevs.team",
                   "port" => "465",
                   "encryption" => "ssl",
                   "username" => "noreply@appdevs.team",
                   "password" => "QP2fsLk?80Ac",
                   "from" => "noreply@appdevs.team",
                   "mail_address"  => "noreply@appdevs.team",
                   'app_name'      => 'DoctorHub'
               ],

            'mail_activity' => NULL,

            'push_notification_config' => [
                'method' => 'pusher',
                'instance_id' => '3488cd8e-e72f-4bb3-9de1-9e8e0511d5b6',
                'primary_key' => '2AC53D6674DCB87FC56834DA05C7F900F54DE998E234B94C4B158B5F2E2B427F'
            ],

            'push_notification_activity' => NULL,

            'broadcast_config' => [
                'method' => 'pusher',
                'app_id' => '',
                'primary_key' => '',
                'secret_key' => '',
                'cluster' => 'ap2'
            ],

            'broadcast_activity' => NULL,

            'sms_config' => NULL,
            'sms_activity' => NULL,

            'web_version' => '1.1.0',
            'admin_version' => '2.5.0',

            'hospital_registration' => '1',
            'hospital_secure_password' => '0',
            'hospital_agree_policy' => '0',
            'hospital_email_verification' => '0',
            'hospital_sms_verification' => '0',
            'hospital_email_notification' => '0',
            'hospital_push_notification' => '1',
            'hospital_kyc_verification' => '1',

            'created_at' => '2025-02-07 17:38:48',
            'updated_at' => '2025-02-07 17:58:59'
        ];

        try {
            $basic_data     = BasicSettings::firstOrCreate($data);
            $env_modify_keys = [
                "MAIL_MAILER"       => $basic_data->mail_config->method,
                "MAIL_HOST"         => $basic_data->mail_config->host,
                "MAIL_PORT"         => $basic_data->mail_config->port,
                "MAIL_USERNAME"     => $basic_data->mail_config->username,
                "MAIL_PASSWORD"     => $basic_data->mail_config->password,
                "MAIL_ENCRYPTION"   => $basic_data->mail_config->encryption,
                "MAIL_FROM_ADDRESS" => $basic_data->mail_config->mail_address,
                "MAIL_FROM_NAME"    => $basic_data->mail_config->app_name,
                "PUSHER_APP_ID"     => $basic_data->broadcast_config->app_id,
                "PUSHER_APP_KEY"    => $basic_data->broadcast_config->primary_key,
                "PUSHER_APP_SECRET" => $basic_data->broadcast_config->secret_key,
            ];
            modifyEnv($env_modify_keys);
        } catch (Exception $e) {
        }
    }
}
