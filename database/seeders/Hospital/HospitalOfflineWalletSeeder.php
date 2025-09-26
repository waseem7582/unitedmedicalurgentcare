<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\HospitalOfflineWallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HospitalOfflineWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospital_offline_wallets = array(
            array('id' => '1','hospital_id' => '1','balance' => '100.00000000','status' => '1','created_at' => '2024-12-12 20:08:04','updated_at' => NULL),
            array('id' => '2','hospital_id' => '2','balance' => '0.00000000','status' => '1','created_at' => '2024-12-12 20:08:04','updated_at' => NULL)
        );
        HospitalOfflineWallet::insert($hospital_offline_wallets);
    }
}
