<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AppSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [

           'id' => '1','version' => '1.1.0','splash_screen_image' => '7b0eff43-748b-4a8e-9607-056520f2de1f.webp','hospital_version' => '1.1.0','hospital_splash_screen_image' => 'fa7b19e9-eebf-42eb-8064-cfbf7e1351f7.webp','url_title' => NULL,'android_url' => NULL,'iso_url' => NULL,'created_at' => '2025-02-10 15:09:11','updated_at' => '2025-04-28 10:57:17'

        ];

        AppSettings::firstOrCreate($data);
    }
}
