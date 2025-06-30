<?php

namespace JordanPartridge\StravaClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JordanPartridge\StravaClient\Data\Webhooks\WebhookEventData;

class ActivityUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly WebhookEventData $event,
    ) {}
}