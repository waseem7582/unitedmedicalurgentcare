<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('id' => '1','hospital_id' => '1','name' => 'Mirpur','uuid' => 'gfh84f6gh7-f6gh76f8gh7','slug' => 'mirpur','status' => '1','created_at' => '2025-03-11 15:47:43','updated_at' => '2025-03-11 15:47:43'),
            array('id' => '2','hospital_id' => '1','name' => 'Kushtia','uuid' => 'fgh54f6g6f-fghf5g7h6f','slug' => 'kushtia','status' => '1','created_at' => '2025-03-11 15:47:55','updated_at' => '2025-03-11 15:47:55'),
            array('id' => '3','hospital_id' => '1','name' => 'Uttora','uuid' => 'fg5h4f6g5h-f35gh4fgh4763f','slug' => 'uttora','status' => '1','created_at' => '2025-03-11 15:49:02','updated_at' => '2025-03-11 15:49:02'),
            array('id' => '4','hospital_id' => '1','name' => 'Dhanmondi','uuid' => 'fghfgh36574-fgh35f47gh6','slug' => 'dhanmondi','status' => '1','created_at' => '2025-03-11 15:49:23','updated_at' => '2025-03-11 15:49:23')

        ];

        Branch::insert($data);
    }
}
