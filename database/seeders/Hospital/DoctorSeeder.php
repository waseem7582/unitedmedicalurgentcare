<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('id' => '1', 'hospital_id' => '1', 'branch_id' => '4', 'department_id' => '1', 'name' => 'Dr. Patrick Soon', 'image' => 'doctor-1.webp', 'title' => 'Senior Consultant', 'qualification' => 'MBBS, DLO', 'specialty' => 'Gastroenterologist', 'language' => 'English,French', 'designation' => 'Gastroenterologist', 'contact' => '12213321212', 'off_days' => '0,1,2,4,6', 'floor_number' => '299', 'room_number' => '101', 'address' => 'Dhaka', 'fees' => '500', 'slug' => 'ea39921f-2591-4144-9d95-1c31c0e10aa8', 'status' => '1', 'created_at' => '2025-03-19 14:22:45', 'updated_at' => '2025-03-19 14:22:45'),
            array('id' => '2', 'hospital_id' => '1', 'branch_id' => '4', 'department_id' => '4', 'name' => 'Dr. Arthur Reese', 'image' => 'doctor-2.webp', 'title' => 'Head Of The Department', 'qualification' => 'MBBS, FCPS', 'specialty' => 'Medicine', 'language' => 'English,Spanish', 'designation' => 'Head Of The Department', 'contact' => '045214777', 'off_days' => '0,1,2,3,5', 'floor_number' => '102', 'room_number' => '511', 'address' => 'Mirpur', 'fees' => '100', 'slug' => '9fbf0770-e5aa-45a8-be3b-83acafe020d3', 'status' => '1', 'created_at' => '2025-03-19 14:27:26', 'updated_at' => '2025-03-19 14:27:26')

        ];
        Doctor::insert($data);
    }
}
