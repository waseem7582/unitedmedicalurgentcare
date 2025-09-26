<?php

namespace App\Constants;

class GlobalConst
{
    const USER_PASS_RESEND_TIME_MINUTE = "1";
    const USEFUL_LINK_PRIVACY_POLICY   = "PRIVACY_POLICY";

    const ACTIVE                = true;
    const BANNED                = false;
    const SUCCESS               = true;
    const DEFAULT_TOKEN_EXP_SEC = 3600;
    const BOOKING_EXP_SEC       = 20000;

    const VERIFIED              = 1;
    const APPROVED              = 1;
    const PENDING               = 2;
    const REJECTED              = 3;
    const DEFAULT               = 0;
    const UNVERIFIED            = 0;

    const USER                  = "USER";
    const ADMIN                 = "ADMIN";
    const HOSPITAL              = "HOSPITAL";


    const PAYMENT               = "payment";

    const RUNNING               = 2;
    const COMPLETE              = 1;
    const CANCEL                = 3;

    const PROFIT                = "PROFIT";

    const UNKNOWN               = "UNKNOWN";

    const MALE                  = "Male";
    const FEMALE                = "Female";
    const OTHERS                = "Others";

    const CASH_PAYMENT          = "cash";
    const ONLINE_PAYMENT        = "online";

    const STATUS_SUCCESS         = 1;
    const STATUS_PENDING         = 2;
    const STATUS_REJECTED        = 3;
    const STATUS_DISABLE         = 4;

 
    const Home_Service           = 1;
    const Investigation          = 2;

    const SYSTEM_MAINTENANCE     = "system-maintenance";

    
    const ENV_SANDBOX    = "sandbox";
    const ENV_PRODUCTION = "production";

}
