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
     *
     * @param string $code
     * @param string $grant_type
     * @return array
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array;

    /**
     * Set authentication tokens.
     *
     * @param string $access_token
     * @param string $refresh_token
     * @return self
     */
    public function setToken(string $access_token, string $refresh_token): self;

    /**
     * Get activities for authenticated athlete.
     *
     * @param array $params
     * @return array
     */
    public function activityForAthlete(array $params = []): array;

    /**
     * Get a specific activity by ID.
     *
     * @param int $id
     * @return array
     */
    public function getActivity(int $id): array;

    /**
     * Create a webhook subscription.
     *
     * @param string|null $callbackUrl
     * @param string|null $verifyToken
     * @return SubscriptionData
     */
    public function createWebhookSubscription(?string $callbackUrl = null, ?string $verifyToken = null): SubscriptionData;

    /**
     * Delete a webhook subscription.
     *
     * @param int $subscriptionId
     * @return bool
     */
    public function deleteWebhookSubscription(int $subscriptionId): bool;

    /**
     * View current webhook subscriptions.
     *
     * @return SubscriptionData[]
     */
    public function viewWebhookSubscriptions(): array;
}