<?php

namespace JordanPartridge\StravaClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JordanPartridge\StravaClient\Data\Webhooks\WebhookEventData;

class ActivityDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly WebhookEventData $event,
    ) {}
}