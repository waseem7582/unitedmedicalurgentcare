<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\Investigation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('id' => '1','hospital_id' => '1','name' => 'Angiography','slug' => 'angiography','uuid' => 'afe84615-89ff-46a1-8cb8-29769d0fe2cc','regular_price' => '3','offer_price' => '2','status' => '1','created_at' => '2025-03-29 09:43:49','updated_at' => '2025-03-29 09:43:49'),
            array('id' => '2','hospital_id' => '1','name' => 'blood test','slug' => 'blood-test','uuid' => 'f30725b2-d638-4598-b899-ddf28752618b','regular_price' => '5','offer_price' => '3','status' => '1','created_at' => '2025-03-29 09:44:10','updated_at' => '2025-03-29 09:44:10'),
            array('id' => '3','hospital_id' => '1','name' => 'pressure test','slug' => 'pressure-test','uuid' => '49b28e8d-0a3b-4b41-8fca-be448b1e502c','regular_price' => '3','offer_price' => '1','status' => '1','created_at' => '2025-03-29 09:44:38','updated_at' => '2025-03-29 09:44:38')

        ];

        Investigation::insert( $data );
    }
}
