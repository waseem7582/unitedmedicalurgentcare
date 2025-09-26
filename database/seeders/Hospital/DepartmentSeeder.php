<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\Departments;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('id' => '1','hospital_id' => '1','uuid' => 'sdf54sd6af5sd6-s5d4f6sdf','name' => 'Dental','description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s','slug' => 'dental','status' => '1','created_at' => '2025-03-11 15:45:18','updated_at' => '2025-03-11 15:45:18'),
            array('id' => '2','hospital_id' => '1','uuid' => '54sdaf6sda6-sad5f66sadf','name' => 'Ophthalmology','description' => NULL,'slug' => 'ophthalmology','status' => '1','created_at' => '2025-03-11 15:45:29','updated_at' => '2025-03-11 15:45:29'),
            array('id' => '3','hospital_id' => '1','uuid' => 's5df476s-sd68f76s8dfsd','name' => 'Pediatric & Pulmonology','description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s','slug' => 'pediatric-pulmonology','status' => '1','created_at' => '2025-03-11 15:45:42','updated_at' => '2025-03-11 15:45:42'),
            array('id' => '4','hospital_id' => '1','uuid' => '65sd4f6s5df-s35ad74f6sdf7','name' => 'Pediatric Surgery','description' => NULL,'slug' => 'pediatric-surgery','status' => '1','created_at' => '2025-03-11 15:45:54','updated_at' => '2025-03-11 15:45:54'),
            array('id' => '5','hospital_id' => '1','uuid' => 'sd4f65sd-sa53d7f6sdf6','name' => 'Nephrology','description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s','slug' => 'nephrology','status' => '1','created_at' => '2025-03-11 15:46:04','updated_at' => '2025-03-11 15:46:04'),
            array('id' => '6','hospital_id' => '1','uuid' => 'sdaf56475-sdf56s7df5','name' => 'Pediatric Surgery','description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s','slug' => 'pediatric-surgery','status' => '1','created_at' => '2025-03-11 15:46:23','updated_at' => '2025-03-11 15:46:23')

        ];

        Departments::insert($data);
    }
}
