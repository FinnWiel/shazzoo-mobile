<?php

namespace FinnWiel\ShazzooMobile;

use FinnWiel\ShazzooMobile\Console\Commands\SendNotification;
use FinnWiel\ShazzooMobile\Console\Commands\SyncNotificationTypes;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;

class ShazzooMobileServiceProvider extends ServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('shazzoo-mobile')
            ->hasConfigFile()
            ->hasCommands([
                SendNotification::class,
            ])
            ->hasMigrations([
                'create_notification_types_table',
                'create_expo_tokens_table',
                'create_device_notification_preferences_table',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            })
            ->hasRoutes('api');
    }
}
