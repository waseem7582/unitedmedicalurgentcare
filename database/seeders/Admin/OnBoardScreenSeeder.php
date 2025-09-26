<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AppOnboardScreens;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OnBoardScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            $data = [
                array('id' => '1','title' => '"Find the Doctor Fast"','sub_title' => '"Search and connect with doctors by specialty and availability in seconds."','image' => 'f4d49cdd-a5c5-4a31-b7b2-177a395889d8.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-04-28 11:06:43','updated_at' => '2025-04-28 11:06:43','type' => 'USER'),
                array('id' => '2','title' => '"Book Care in a Click"','sub_title' => '"Easily schedule appointments with your preferred doctor or hospital at your convenience"','image' => 'd9bec22f-ce76-4ebd-b3cc-70720ab6e032.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-04-28 11:07:35','updated_at' => '2025-04-28 11:07:35','type' => 'USER'),
                array('id' => '3','title' => '"Fast & Secure Payments"','sub_title' => '"Securely complete payments for appointments, services, or bills\\u2014right from your device."','image' => '0488bdfb-ce40-482a-81e5-57cb11a08a92.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-04-28 11:08:17','updated_at' => '2025-04-28 11:08:17','type' => 'USER'),
                array('id' => '4','title' => '"Manage Doctors & Bookings"','sub_title' => '"Efficiently manage doctor profiles and patient bookings everything in one organized space."','image' => '27cb9774-4a74-44bb-ad60-041ee74db5c3.webp','status' => '1','last_edit_by' => '1','created_at' => '2025-04-28 11:10:31','updated_at' => '2025-04-28 11:10:31','type' => 'HOSPITAL')

            ];

            AppOnboardScreens::insert($data);
    }
}
