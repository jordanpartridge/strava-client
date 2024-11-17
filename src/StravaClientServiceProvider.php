<?php

namespace JordanPartridge\StravaClient;

use JordanPartridge\StravaClient\Commands\StravaClientCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StravaClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('strava-client')
            ->hasConfigFile()
            ->hasMigration('create_strava_client_table')
            ->hasCommand(StravaClientCommand::class);
    }

    public function packageBooted(): void
    {
        $this->app->bind('strava-client', function () {
            new StravaClient(new Connector(), config('strava-client.max_refresh_attempts'));
        });

        parent::packageBooted();
    }
}
