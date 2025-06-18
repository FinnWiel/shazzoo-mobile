<?php

namespace FinnWiel\ShazzooNotify;

use FinnWiel\ShazzooNotify\Console\Commands\SendNotification;
use FinnWiel\ShazzooNotify\Console\Commands\SyncNotificationTypes;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;

class ShazzooNotifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/channels.php');

        $this->commands([
            SendNotification::class,
        ]);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('shazzoo-notify')
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
