<?php

namespace App\Traits\Hospital;

use App\Models\Admin\Currency;
use App\Models\Hospital\HospitalLoginLog;
use App\Models\Hospital\HospitalWallet;
use Exception;
use Jenssegers\Agent\Agent;

trait LoggedInHospitals
{


    protected function refreshUserWallets($user)
    {
        $user_wallets = $user->wallets->pluck("currency_id")->toArray();
        $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
        $new_currencies = array_diff($currencies, $user_wallets);
        $new_wallets = [];
        foreach ($new_currencies as $item) {
            $new_wallets[] = [
                'hospital_id'     => $user->id,
                'currency_id'   => $item,
                'balance'       => 0,
                'status'        => true,
                'created_at'    => now(),
            ];
        }
        try {
            HospitalWallet::insert($new_wallets);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    protected function createLoginLog($hospital)
    {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);

        $agent = new Agent();

        $mac = "";

        $data = [
            'hospital_id'     => $hospital->id,
            'ip'            => $client_ip,
            'mac'           => $mac,
            'city'          => $location['city'] ?? "",
            'country'       => $location['country'] ?? "",
            'longitude'     => $location['lon'] ?? "",
            'latitude'      => $location['lat'] ?? "",
            'timezone'      => $location['timezone'] ?? "",
            'browser'       => $agent->browser() ?? "",
            'os'            => $agent->platform() ?? "",
        ];

        try {
            HospitalLoginLog::create($data);
        } catch (Exception $e) {
            //

        }
    }
}
