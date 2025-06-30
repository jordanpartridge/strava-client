<?php

use Carbon\Carbon;
use JordanPartridge\StravaClient\Data\Webhooks\WebhookEventData;

it('creates webhook event data from array', function () {
    $data = [
        'aspect_type' => 'create',
        'event_time' => 1234567890,
        'object_id' => 12345,
        'object_type' => 'activity',
        'owner_id' => 67890,
        'subscription_id' => 1,
        'updates' => ['title' => true],
    ];

    $event = WebhookEventData::fromArray($data);

    expect($event->aspect_type)->toBe('create');
    expect($event->event_time)->toBe(1234567890);
    expect($event->object_id)->toBe(12345);
    expect($event->object_type)->toBe('activity');
    expect($event->owner_id)->toBe(67890);
    expect($event->subscription_id)->toBe(1);
    expect($event->updates)->toBe(['title' => true]);
});

it('returns carbon instance for event time', function () {
    $event = new WebhookEventData(
        aspect_type: 'create',
        event_time: 1234567890,
        object_id: 12345,
        object_type: 'activity',
        owner_id: 67890,
        subscription_id: 1,
    );

    $eventTime = $event->getEventTime();

    expect($eventTime)->toBeInstanceOf(Carbon::class);
    expect($eventTime->timestamp)->toBe(1234567890);
});

it('identifies activity events correctly', function () {
    $activityEvent = new WebhookEventData(
        aspect_type: 'create',
        event_time: 1234567890,
        object_id: 12345,
        object_type: 'activity',
        owner_id: 67890,
        subscription_id: 1,
    );

    $athleteEvent = new WebhookEventData(
        aspect_type: 'update',
        event_time: 1234567890,
        object_id: 67890,
        object_type: 'athlete',
        owner_id: 67890,
        subscription_id: 1,
    );

    expect($activityEvent->isActivityEvent())->toBeTrue();
    expect($activityEvent->isAthleteEvent())->toBeFalse();
    expect($athleteEvent->isActivityEvent())->toBeFalse();
    expect($athleteEvent->isAthleteEvent())->toBeTrue();
});

it('identifies aspect types correctly', function () {
    $createEvent = new WebhookEventData(
        aspect_type: 'create',
        event_time: 1234567890,
        object_id: 12345,
        object_type: 'activity',
        owner_id: 67890,
        subscription_id: 1,
    );

    $updateEvent = new WebhookEventData(
        aspect_type: 'update',
        event_time: 1234567890,
        object_id: 12345,
        object_type: 'activity',
        owner_id: 67890,
        subscription_id: 1,
    );

    $deleteEvent = new WebhookEventData(
        aspect_type: 'delete',
        event_time: 1234567890,
        object_id: 12345,
        object_type: 'activity',
        owner_id: 67890,
        subscription_id: 1,
    );

    expect($createEvent->isCreate())->toBeTrue();
    expect($createEvent->isUpdate())->toBeFalse();
    expect($createEvent->isDelete())->toBeFalse();

    expect($updateEvent->isCreate())->toBeFalse();
    expect($updateEvent->isUpdate())->toBeTrue();
    expect($updateEvent->isDelete())->toBeFalse();

    expect($deleteEvent->isCreate())->toBeFalse();
    expect($deleteEvent->isUpdate())->toBeFalse();
    expect($deleteEvent->isDelete())->toBeTrue();
});

it('identifies deauthorization events', function () {
    $deauthEvent = new WebhookEventData(
        aspect_type: 'update',
        event_time: 1234567890,
        object_id: 67890,
        object_type: 'athlete',
        owner_id: 67890,
        subscription_id: 1,
        updates: ['authorized' => 'false'],
    );

    $normalAthleteEvent = new WebhookEventData(
        aspect_type: 'update',
        event_time: 1234567890,
        object_id: 67890,
        object_type: 'athlete',
        owner_id: 67890,
        subscription_id: 1,
        updates: ['profile' => 'updated'],
    );

    expect($deauthEvent->isDeauthorization())->toBeTrue();
    expect($normalAthleteEvent->isDeauthorization())->toBeFalse();
});