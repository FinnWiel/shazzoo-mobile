<?php

namespace FinnWiel\ShazzooNotify;

use FinnWiel\ShazzooNotify\Console\Commands\SendNotification;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class ShazzooNotifyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('shazzoo-notify')
            ->hasCommands([
                SendNotification::class,
            ])
            ->hasMigrations([
                'create_notification_types_table',
                'create_registered_devices_table',
                'create_device_notification_preferences_table',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations();
            })
            ->hasRoutes('api');
    }

    public function bootingPackage()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/channels.php');
    }
}
