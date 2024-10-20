<?php

namespace JordanPartridge\StravaClient\Commands;

use Illuminate\Console\Command;

class StravaClientCommand extends Command
{
    public $signature = 'strava-client:install';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
