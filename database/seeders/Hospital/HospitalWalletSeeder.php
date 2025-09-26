<?php

namespace Database\Seeders\Hospital;

use App\Models\Hospital\HospitalWallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HospitalWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hospital_wallets = array(
            array('hospital_id' => '1','currency_id' => '1','balance' => '100.00000000','status' => '1','created_at' => '2024-12-12 20:08:04','updated_at' => NULL),
            array('hospital_id' => '2','currency_id' => '1','balance' => '0.00000000','status' => '1','created_at' => '2024-12-12 20:08:04','updated_at' => NULL)

        );
        HospitalWallet::insert($hospital_wallets);
    }
}
