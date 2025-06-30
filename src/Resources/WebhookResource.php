<?php

namespace JordanPartridge\StravaClient\Resources;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Data\Webhooks\SubscriptionData;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

/**
 * Webhook Resource
 * 
 * Provides a clean, type-safe interface for managing Strava webhook subscriptions.
 */
class WebhookResource extends BaseResource
{
    /**
     * Create a new webhook subscription.
     *
     * @param string|null $callbackUrl The URL that will receive webhook events
     * @param string|null $verifyToken Token used for webhook verification
     * @return SubscriptionData
     * @throws InvalidArgumentException
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function create(?string $callbackUrl = null, ?string $verifyToken = null): SubscriptionData
    {
        $callbackUrl ??= config('strava-client.webhook.callback_url');
        $verifyToken ??= config('strava-client.webhook.verify_token');

        if (empty($callbackUrl) || empty($verifyToken)) {
            throw new InvalidArgumentException('Callback URL and verify token are required for webhook subscription');
        }

        $response = $this->handleRequest(
            fn () => $this->connector->createWebhookSubscription($callbackUrl, $verifyToken)
        );

        return SubscriptionData::fromArray($response);
    }

    /**
     * Delete a webhook subscription.
     *
     * @param int $subscriptionId The ID of the subscription to delete
     * @return bool
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function delete(int $subscriptionId): bool
    {
        $this->handleRequest(
            fn () => $this->connector->deleteWebhookSubscription($subscriptionId)
        );

        return true;
    }

    /**
     * List all current webhook subscriptions.
     *
     * @return SubscriptionData[]
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function list(): array
    {
        $response = $this->handleRequest(
            fn () => $this->connector->viewWebhookSubscriptions()
        );

        return array_map(
            fn (array $subscription) => SubscriptionData::fromArray($subscription),
            $response
        );
    }

    /**
     * Get a specific webhook subscription by ID.
     * 
     * Note: Strava API doesn't provide individual subscription lookup,
     * so this method filters the list of all subscriptions.
     *
     * @param int $subscriptionId
     * @return SubscriptionData|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function find(int $subscriptionId): ?SubscriptionData
    {
        $subscriptions = $this->list();

        foreach ($subscriptions as $subscription) {
            if ($subscription->id === $subscriptionId) {
                return $subscription;
            }
        }

        return null;
    }

    /**
     * Check if any webhook subscriptions exist.
     *
     * @return bool
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function exists(): bool
    {
        return count($this->list()) > 0;
    }

    /**
     * Get the first (and typically only) webhook subscription.
     *
     * @return SubscriptionData|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function first(): ?SubscriptionData
    {
        $subscriptions = $this->list();

        return $subscriptions[0] ?? null;
    }
}