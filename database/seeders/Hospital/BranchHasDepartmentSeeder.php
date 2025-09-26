<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\BranchHasDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchHasDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [

            array('id' => '1','branch_id' => '1','department_id' => '1','created_at' => '2025-03-11 15:47:43','updated_at' => '2025-03-11 15:47:43'),
            array('id' => '2','branch_id' => '2','department_id' => '2','created_at' => '2025-03-11 15:47:55','updated_at' => '2025-03-11 15:47:55'),
            array('id' => '3','branch_id' => '2','department_id' => '4','created_at' => '2025-03-11 15:47:55','updated_at' => '2025-03-11 15:47:55'),
            array('id' => '4','branch_id' => '3','department_id' => '2','created_at' => '2025-03-11 15:49:02','updated_at' => '2025-03-11 15:49:02'),
            array('id' => '5','branch_id' => '3','department_id' => '4','created_at' => '2025-03-11 15:49:02','updated_at' => '2025-03-11 15:49:02'),
            array('id' => '6','branch_id' => '3','department_id' => '5','created_at' => '2025-03-11 15:49:02','updated_at' => '2025-03-11 15:49:02'),
            array('id' => '7','branch_id' => '3','department_id' => '6','created_at' => '2025-03-11 15:49:02','updated_at' => '2025-03-11 15:49:02'),
            array('id' => '8','branch_id' => '4','department_id' => '1','created_at' => '2025-03-11 15:49:23','updated_at' => '2025-03-11 15:49:23'),
            array('id' => '9','branch_id' => '4','department_id' => '2','created_at' => '2025-03-11 15:49:23','updated_at' => '2025-03-11 15:49:23'),
            array('id' => '10','branch_id' => '4','department_id' => '4','created_at' => '2025-03-11 15:49:23','updated_at' => '2025-03-11 15:49:23'),
            array('id' => '11','branch_id' => '4','department_id' => '6','created_at' => '2025-03-11 15:49:23','updated_at' => '2025-03-11 15:49:23')

        ];

        BranchHasDepartment::insert($data);
    }
}
