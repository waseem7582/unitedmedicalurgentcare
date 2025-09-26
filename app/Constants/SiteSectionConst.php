<?php

namespace App\Constants;

class SiteSectionConst
{
    const BANNER_SECTION            = "Banner Section";
    const PROJECT_FEATURE           = "Project Feature";
    const FEATURES_SECTION          = "Features Section";
    const HOW_ITS_WORK_SECTION      = "How Its Work Section";
    const WHY_CHOICE_US_SECTION     = "Why Choice Us Section";
    const STATISTICS                = "Statistics";
    const DOWNLOAD_APP_SECTION      = "Download App";
    const ABOUT_US_SECTION          = "About Us Section";
    const FAQ_SECTION               = "Faq";
    const SERVICES_SECTION          = "Services Section";
    const CLIENT_FEEDBACK_SECTION   = "Client Feedback Section";
    const CONTACT_SECTION           = "Contact";
    const BLOG_SECTION              = "Blog";
    const HOW_IT_WORK_SECTION       = "How It Work";
    const FOOTER_SECTION            = "Footer Section";
    const ABOUT_PAGE_SECTION        = "About Page Section";
    const CONTACT_US_SECTION        = "Contact Us Section";
    const LOGIN_SECTION             = "Auth Section";
    const VENDOR_BANNER_SECTION     = "Hospital Banner Section";
    const VENDOR_FEATURES_SECTION   = "Hospital Features Section";
    const VENDOR_REQUIREMENTS_SECTION   = "Hospital Requirements Section";

     const NOT_DISPLAY_COOKIE_SECTION     = "site_cookie";
    const NOT_DISPLAY_AUTH_SECTION       = "auth-section";
    const NOT_DISPLAY_FOOTER_SECTION     = "footer-section";

      public static function notDisplaySections(): array
    {
        return [
            self::NOT_DISPLAY_COOKIE_SECTION,
            self::NOT_DISPLAY_AUTH_SECTION,
            self::NOT_DISPLAY_FOOTER_SECTION
        ];
    }
}
