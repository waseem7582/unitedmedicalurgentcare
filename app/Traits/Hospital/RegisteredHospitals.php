<?php

namespace App\Traits\Hospital;

trait RegisteredHospitals {

    protected function breakAuthentication($error) {
        return back()->with(['error' => [$error]]);
    }
}
