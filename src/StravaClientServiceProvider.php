<?php

namespace JordanPartridge\StravaClient;

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
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('strava-client')
            ->hasConfigFile()
            ->hasRoute('strava')
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
          return  new StravaClient(
                strava: new Connector,
                max_refresh_attempts: config('strava-client.max_refresh_attempts')
            );
        });

        parent::packageBooted();
    }
}
