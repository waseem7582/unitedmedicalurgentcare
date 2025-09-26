<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SetupPageHasSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionHasPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $setup_page_has_sections = array(
          

            array('id' => '13','setup_page_id' => '3','site_section_id' => '12','position' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('id' => '14','setup_page_id' => '3','site_section_id' => '13','position' => '2','status' => '1','created_at' => now(),'updated_at' => now()),
            array('id' => '15','setup_page_id' => '3','site_section_id' => '14','position' => '3','status' => '1','created_at' => now(),'updated_at' => now()),


            array('id' => '25','setup_page_id' => '4','site_section_id' => '6','position' => '1','status' => '1','created_at' => now(),'updated_at' => now()),
            array('id' => '26','setup_page_id' => '4','site_section_id' => '7','position' => '2','status' => '1','created_at' => now(),'updated_at' => now()),
            array('id' => '27','setup_page_id' => '4','site_section_id' => '10','position' => '3','status' => '1','created_at' => now(),'updated_at' => now()),
           
           
            array('id' => '37','setup_page_id' => '5','site_section_id' => '9','position' => '2','status' => '1','created_at' => now(),'updated_at' => now()),
           
             array('id' => '38','setup_page_id' => '6','site_section_id' => '8','position' => '2','status' => '1','created_at' => now(),'updated_at' => now()),
           
            
           
        );

        SetupPageHasSection::upsert($setup_page_has_sections,['id'],['setup_page_id','site_section_id','position','status']);
    }
}
