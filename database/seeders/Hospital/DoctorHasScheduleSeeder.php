<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\DoctorHasSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorHasScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            array('id' => '1','doctor_id' => '1','day' => '3','from_time' => '17:32','to_time' => '16:37','max_client' => '84','status' => '1','created_at' => '2025-03-20 16:38:27','updated_at' => NULL),
            array('id' => '2','doctor_id' => '2','day' => '4','from_time' => '14:44','to_time' => '21:38','max_client' => '6','status' => '1','created_at' => '2025-03-20 16:38:27','updated_at' => NULL),

            array('id' => '3','doctor_id' => '1','day' => '5','from_time' => '12:32','to_time' => '16:37','max_client' => '84','status' => '1','created_at' => '2025-03-20 16:38:27','updated_at' => NULL),
            array('id' => '4','doctor_id' => '2','day' => '6','from_time' => '10:44','to_time' => '21:38','max_client' => '6','status' => '1','created_at' => '2025-03-20 16:38:27','updated_at' => NULL)

        ];

        DoctorHasSchedule::insert($data);
    }
}
