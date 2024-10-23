<?php

namespace JordanPartridge\StravaClient\Commands;

use Illuminate\Console\Command;

class StravaClientCommand extends Command
{
    public $signature = 'strava-client:install';

    public $description = 'Not sure if we will need a command yet, but this could allow some customized setup';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
