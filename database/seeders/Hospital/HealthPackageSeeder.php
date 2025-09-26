<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\HealthPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HealthPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('id' => '1','hospital_id' => '1','name' => 'HCP08','title' => 'HCP08','description' => 'this is test description','regular_price' => '5','offer_price' => '3','slug' => 'hcp08','uuid' => '32e924f3-0e77-45c5-8c37-f7af10f77fa4','status' => '1','created_at' => '2025-03-29 09:37:21','updated_at' => '2025-03-29 09:37:21'),
            array('id' => '2','hospital_id' => '1','name' => 'HCP07','title' => 'HCP07','description' => 'this is test description','regular_price' => '6','offer_price' => '8','slug' => 'hcp07','uuid' => '55c6cc02-b1a9-4aa3-92ae-dbc895cdc72f','status' => '1','created_at' => '2025-03-29 09:37:40','updated_at' => '2025-03-29 09:37:40'),
            array('id' => '3','hospital_id' => '1','name' => 'HCP05','title' => 'HCP05','description' => 'this is test description','regular_price' => '15','offer_price' => '10','slug' => 'hcp05','uuid' => 'a3038e6c-657e-413a-b3a0-57e09fb60353','status' => '1','created_at' => '2025-03-29 09:38:02','updated_at' => '2025-03-29 09:38:02')
        ];

        HealthPackage::insert( $data );
    }
}
