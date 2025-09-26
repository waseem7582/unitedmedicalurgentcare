<?php

namespace App\Http\Middleware\Hospital;

use Closure;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Providers\Admin\BasicSettingsProvider;

use App\Http\Helpers\Response;

class KycVerificationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        $basic_settings = BasicSettingsProvider::get();

        if ($basic_settings->hospital_kyc_verification) {
            $user = auth()->user();

            if ($user->kyc_verified != GlobalConst::APPROVED) {
                $msg = __("Please verify your KYC information before any action");

                if ($user->kyc_verified == GlobalConst::PENDING) {
                    $msg = __("Your KYC information is pending. Please wait for admin confirmation");
                }
                // Handle API requests
                if ($request->expectsJson() || $request->is('api/*')) {
                    return Response::error($msg, null, 422);
                }
                // Handle web requests
                if (auth()->guard("hospital")->check()) {
                    return redirect()
                        ->route("hospitals.authorize.kyc")
                        ->with(['warning' => [$msg]]);
                }
            }
        }

        return $next($request);
    }
}
