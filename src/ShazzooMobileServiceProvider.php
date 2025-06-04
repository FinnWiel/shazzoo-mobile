<?php

namespace FinnWiel\ShazzooMobile;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShazzooMobileServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('shazzoo-mobile')
            ->hasConfigFile()
            ->hasMigrations([
                'create_notification_types_table',
                'create_expo_tokens_table',
                'create_device_notification_preferences_table',
            ])
            ->hasRoutes('api');
    }
}
