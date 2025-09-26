<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\InvestigationHasCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestigationHasCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            array('id' => '1','investigation_id' => '3','investigation_category_id' => '1','created_at' => '2025-04-21 16:59:21','updated_at' => '2025-04-21 16:59:21'),
            array('id' => '2','investigation_id' => '2','investigation_category_id' => '1','created_at' => '2025-04-21 16:59:28','updated_at' => '2025-04-21 16:59:28'),
            array('id' => '3','investigation_id' => '2','investigation_category_id' => '2','created_at' => '2025-04-21 16:59:28','updated_at' => '2025-04-21 16:59:28'),
            array('id' => '4','investigation_id' => '1','investigation_category_id' => '2','created_at' => '2025-04-21 16:59:33','updated_at' => '2025-04-21 16:59:33')
        ];
        InvestigationHasCategory::insert($data);
    }
}
