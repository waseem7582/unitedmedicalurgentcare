<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\InvestigationCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestigationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('id' => '1','name' => 'Home Service','slug' => 'Home Service','uuid' => '56s4df56-sadfksdaf-6sa5d4f65s','remark' => '','status' => '1','created_at' => NULL,'updated_at' => NULL),
            array('id' => '2','name' => 'Investigation','slug' => 'Home Service','uuid' => '65sda4f65sad4g6sd-65a4df6asdf45sda-65sadf46','remark' => '','status' => '1','created_at' => NULL,'updated_at' => NULL)
          
        ];
        InvestigationCategory::insert($data);
    }
}
