<?php

namespace JordanPartridge\StravaClient\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use JordanPartridge\StravaClient\StravaClientServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'JordanPartridge\\StravaClient\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations()
    {
        // Create a simple users table for testing
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            StravaClientServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // Set required app key for encryption (32 bytes for AES-256)
        config()->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));

        // Strava config
        config()->set('strava-client.client_id', 'test_client_id');
        config()->set('strava-client.client_secret', 'test_client_secret');
        config()->set('strava-client.redirect_after_connect', '/dashboard');
        config()->set('strava-client.scope', 'read,activity:read_all');
    }
}
