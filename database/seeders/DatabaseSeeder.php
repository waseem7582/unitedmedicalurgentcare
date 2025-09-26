<?php

namespace Database\Seeders;


use App\Models\Hospital\BranchHasDepartment;
use App\Models\Hospital\Hospital;
use App\Models\Hospital\HospitalOfflineWallet;
use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\Admin\CurrencySeeder;
use Database\Seeders\Admin\LanguageSeeder;
use Database\Seeders\Admin\SetupKycSeeder;
use Database\Seeders\Admin\SetupSeoSeeder;
use Database\Seeders\Admin\ExtensionSeeder;
use Database\Seeders\Admin\SetupPageSeeder;
use Database\Seeders\Admin\UsefulLinkSeeder;
use Database\Seeders\Admin\AppSettingsSeeder;
use Database\Seeders\Admin\AdminHasRoleSeeder;
use Database\Seeders\Admin\FreshBasicSettingsSeeder;
use Database\Seeders\Admin\SiteSectionsSeeder;
use Database\Seeders\Admin\BasicSettingsSeeder;
use Database\Seeders\Admin\PaymentGatewaySeeder;
use Database\Seeders\Admin\TransactionSettingSeeder;
use Database\Seeders\Admin\BlogSeeder;
use Database\Seeders\Admin\BlogCategorySeeder;
use Database\Seeders\Admin\SystemMaintenanceSeeder;
use Database\Seeders\Admin\OnBoardScreenSeeder;
use Database\Seeders\Admin\SectionHasPageSeeder;
use Database\Seeders\Hospital\HospitalSeeder;
use Database\Seeders\Hospital\HospitalWalletSeeder;
use Database\Seeders\Hospital\DepartmentSeeder;
use Database\Seeders\Hospital\BranchSeeder;
use Database\Seeders\Hospital\BranchHasDepartmentSeeder;
use Database\Seeders\Hospital\DoctorHasScheduleSeeder;
use Database\Seeders\Hospital\DoctorSeeder;
use Database\Seeders\Hospital\HealthPackageSeeder;
use Database\Seeders\Hospital\HospitalOfflineWalletSeeder;
use Database\Seeders\Hospital\InvestigationCategorySeeder;
use Database\Seeders\Hospital\InvestigationHasCategorySeeder;
use Database\Seeders\Hospital\InvestigationSeeder;




class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // //fresh
        $this->call([
            AdminSeeder::class,
            RoleSeeder::class,
            TransactionSettingSeeder::class,
            CurrencySeeder::class,
            FreshBasicSettingsSeeder::class,
            SetupSeoSeeder::class,
            AppSettingsSeeder::class,
            SiteSectionsSeeder::class,
            BlogCategorySeeder::class,
            BlogSeeder::class,
            SetupPageSeeder::class,
            SetupKycSeeder::class,
            ExtensionSeeder::class,
            AdminHasRoleSeeder::class,
            LanguageSeeder::class,
            UsefulLinkSeeder::class,
            PaymentGatewaySeeder::class,
            SystemMaintenanceSeeder::class,
        ]);

        //demo
        // $this->call([
        //     AdminSeeder::class,
        //     RoleSeeder::class,
        //     TransactionSettingSeeder::class,
        //     SystemMaintenanceSeeder::class,
        //     CurrencySeeder::class,
        //     BasicSettingsSeeder::class,
        //     SetupSeoSeeder::class,
        //     AppSettingsSeeder::class,
        //     SiteSectionsSeeder::class,
        //     BlogCategorySeeder::class,
        //     BlogSeeder::class,
        //     SetupPageSeeder::class,
        //     SetupKycSeeder::class,
        //     ExtensionSeeder::class,
        //     AdminHasRoleSeeder::class,
        //     UserSeeder::class,
        //     HospitalSeeder::class,
        //     LanguageSeeder::class,
        //     UsefulLinkSeeder::class,
        //     PaymentGatewaySeeder::class,
        //     HospitalWalletSeeder::class,
        //     OnBoardScreenSeeder::class,
        //     InvestigationCategorySeeder::class,
        //     DepartmentSeeder::class,
        //     BranchSeeder::class,
        //     BranchHasDepartmentSeeder::class,
        //     DoctorSeeder::class,
        //     DoctorHasScheduleSeeder::class,
        //     HospitalOfflineWalletSeeder::class,
        //     InvestigationSeeder::class,
        //     HealthPackageSeeder::class,
        //     InvestigationHasCategorySeeder::class,
        //     SectionHasPageSeeder::class,
        // ]);
    }
}
