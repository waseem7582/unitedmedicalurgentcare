<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\SetupKyc;

class SetupKycSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setup_kycs = array(
            array('slug' => 'hospital','user_type' => 'VENDOR','fields' => '[{"type":"file","label":"Front","name":"front","required":true,"validation":{"max":"2","mimes":["jpg","png","webp","jpeg"],"min":0,"options":[],"required":true}},{"type":"file","label":"Back","name":"back","required":true,"validation":{"max":"2","mimes":["jpg","png","webp","jpeg"],"min":0,"options":[],"required":true}},{"type":"select","label":"ID Type","name":"id_type","required":true,"validation":{"max":0,"min":0,"mimes":[],"options":["NID"," Driving License"," Passport"],"required":true}}]','status' => '1','last_edit_by' => '1','created_at' => '2024-02-22 05:38:09','updated_at' => '2024-02-22 05:44:50')
          );

        SetupKyc::insert($setup_kycs);
    }
}
