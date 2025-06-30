<?php

namespace JordanPartridge\StravaClient\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use JordanPartridge\StravaClient\Data\Webhooks\WebhookEventData;
use JordanPartridge\StravaClient\Events\ActivityCreated;
use JordanPartridge\StravaClient\Events\ActivityDeleted;
use JordanPartridge\StravaClient\Events\ActivityUpdated;
use JordanPartridge\StravaClient\Events\AthleteDeauthorized;
use JordanPartridge\StravaClient\Exceptions\Webhooks\InvalidWebhookSignatureException;
use JordanPartridge\StravaClient\Services\WebhookVerificationService;

class WebhookController extends Controller
{
    public function __construct(
        private readonly WebhookVerificationService $verificationService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        // Handle Strava's subscription verification challenge
        if ($request->isMethod('GET')) {
            return $this->handleSubscriptionChallenge($request);
        }

        // Verify webhook signature
        if (!$this->verificationService->verify($request)) {
            throw new InvalidWebhookSignatureException('Invalid webhook signature');
        }

        // Process webhook event
        $eventData = WebhookEventData::fromArray($request->all());
        
        $this->dispatchEvent($eventData);

        return response()->json(['status' => 'success']);
    }

    private function handleSubscriptionChallenge(Request $request): JsonResponse
    {
        $verifyToken = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($verifyToken !== config('strava-client.webhook.verify_token')) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid verify token');
        }

        return response()->json(['hub.challenge' => $challenge]);
    }

    private function dispatchEvent(WebhookEventData $event): void
    {
        if ($event->isDeauthorization()) {
            AthleteDeauthorized::dispatch($event);
            return;
        }

        if (!$event->isActivityEvent()) {
            return;
        }

        match ($event->aspect_type) {
            'create' => ActivityCreated::dispatch($event),
            'update' => ActivityUpdated::dispatch($event),
            'delete' => ActivityDeleted::dispatch($event),
            default => null,
        };
    }
}