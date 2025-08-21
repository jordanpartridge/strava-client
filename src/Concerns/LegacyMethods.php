<?php

namespace JordanPartridge\StravaClient\Concerns;

use JordanPartridge\StravaClient\Data\Webhooks\SubscriptionData;

/**
 * Legacy Methods Trait
 *
 * Provides backward compatibility for the original StravaClient API.
 * These methods delegate to the new resource-based architecture internally.
 *
 * @deprecated Will be removed in v1.0.0. Use the resource-based API instead.
 */
trait LegacyMethods
{
    /**
     * Get activities for authenticated athlete.
     *
     * @deprecated Use $client->activities()->list($params) instead.
     */
    public function activityForAthlete(array $params = []): array
    {
        return $this->activities()->list($params);
    }

    /**
     * Get a specific activity by ID.
     *
     * @deprecated Use $client->activities()->get($id) instead.
     */
    public function getActivity(int $id): array
    {
        return $this->activities()->get($id);
    }

    /**
     * Create a webhook subscription.
     *
     * @deprecated Use $client->webhooks()->create($callbackUrl, $verifyToken) instead.
     */
    public function createWebhookSubscription(?string $callbackUrl = null, ?string $verifyToken = null): SubscriptionData
    {
        return $this->webhooks()->create($callbackUrl, $verifyToken);
    }

    /**
     * Delete a webhook subscription.
     *
     * @deprecated Use $client->webhooks()->delete($subscriptionId) instead.
     */
    public function deleteWebhookSubscription(int $subscriptionId): bool
    {
        return $this->webhooks()->delete($subscriptionId);
    }

    /**
     * View current webhook subscriptions.
     *
     * @deprecated Use $client->webhooks()->list() instead.
     *
     * @return SubscriptionData[]
     */
    public function viewWebhookSubscriptions(): array
    {
        return $this->webhooks()->list();
    }
}
