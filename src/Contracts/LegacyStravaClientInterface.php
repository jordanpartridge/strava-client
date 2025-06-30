<?php

namespace JordanPartridge\StravaClient\Contracts;

use JordanPartridge\StravaClient\Data\Webhooks\SubscriptionData;

/**
 * Legacy Strava Client Interface
 *
 * Maintains backward compatibility with the original API.
 *
 * @deprecated Will be removed in v1.0.0. Use StravaResourceClientInterface instead.
 */
interface LegacyStravaClientInterface
{
    /**
     * Exchange authorization code for tokens.
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array;

    /**
     * Set authentication tokens.
     */
    public function setToken(string $access_token, string $refresh_token): self;

    /**
     * Get activities for authenticated athlete.
     */
    public function activityForAthlete(array $params = []): array;

    /**
     * Get a specific activity by ID.
     */
    public function getActivity(int $id): array;

    /**
     * Create a webhook subscription.
     */
    public function createWebhookSubscription(?string $callbackUrl = null, ?string $verifyToken = null): SubscriptionData;

    /**
     * Delete a webhook subscription.
     */
    public function deleteWebhookSubscription(int $subscriptionId): bool;

    /**
     * View current webhook subscriptions.
     *
     * @return SubscriptionData[]
     */
    public function viewWebhookSubscriptions(): array;
}
