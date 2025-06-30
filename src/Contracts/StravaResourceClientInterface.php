<?php

namespace JordanPartridge\StravaClient\Contracts;

use JordanPartridge\StravaClient\Resources\ActivityResource;
use JordanPartridge\StravaClient\Resources\AthleteResource;
use JordanPartridge\StravaClient\Resources\WebhookResource;

/**
 * Modern Resource-based Strava Client Interface
 * 
 * Provides a clean, type-safe resource-based API for Strava integration.
 */
interface StravaResourceClientInterface
{
    /**
     * Access activity-related operations.
     *
     * @return ActivityResource
     */
    public function activities(): ActivityResource;

    /**
     * Access athlete-related operations.
     *
     * @return AthleteResource
     */
    public function athlete(): AthleteResource;

    /**
     * Access webhook-related operations.
     *
     * @return WebhookResource
     */
    public function webhooks(): WebhookResource;

    /**
     * Set authentication tokens for the client.
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @return self
     */
    public function withTokens(string $accessToken, string $refreshToken): self;

    /**
     * Exchange authorization code for tokens.
     *
     * @param string $code
     * @param string $grantType
     * @return array
     */
    public function exchangeToken(string $code, string $grantType = 'authorization_code'): array;
}