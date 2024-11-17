<?php

namespace JordanPartridge\StravaClient;

use JordanPartridge\StravaClient\Commands\StravaClientCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StravaClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https:/L/github.com/spatie/laravel-package-tools
         */
        $package
            ->name('strava-client')
            ->hasConfigFile()
            ->hasMigration('create_strava_tokens_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToStarRepoOnGitHub('jordanpartridge/strava-client');
            });
    }

    public function packageBooted(): void
    {
        $this->app->bind('strava-client', function () {
            new StravaClient(
                strava: new Connector,
                max_refresh_attempts: config('strava-client.max_refresh_attempts')
            );
        });

        parent::packageBooted();
    }
}
