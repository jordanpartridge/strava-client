<?php

namespace JordanPartridge\StravaClient\Data\Webhooks;

use Carbon\Carbon;

readonly class WebhookEventData
{
    public function __construct(
        public string $aspect_type,
        public int $event_time,
        public int $object_id,
        public string $object_type,
        public int $owner_id,
        public int $subscription_id,
        public ?array $updates = null,
    ) {}

    public function getEventTime(): Carbon
    {
        return Carbon::createFromTimestamp($this->event_time);
    }

    public function isActivityEvent(): bool
    {
        return $this->object_type === 'activity';
    }

    public function isAthleteEvent(): bool
    {
        return $this->object_type === 'athlete';
    }

    public function isCreate(): bool
    {
        return $this->aspect_type === 'create';
    }

    public function isUpdate(): bool
    {
        return $this->aspect_type === 'update';
    }

    public function isDelete(): bool
    {
        return $this->aspect_type === 'delete';
    }

    public function isDeauthorization(): bool
    {
        return $this->aspect_type === 'update' && $this->object_type === 'athlete' && 
               isset($this->updates['authorized']) && $this->updates['authorized'] === 'false';
    }

    public static function fromArray(array $data): self
    {
        return new self(
            aspect_type: $data['aspect_type'],
            event_time: $data['event_time'],
            object_id: $data['object_id'],
            object_type: $data['object_type'],
            owner_id: $data['owner_id'],
            subscription_id: $data['subscription_id'],
            updates: $data['updates'] ?? null,
        );
    }
}