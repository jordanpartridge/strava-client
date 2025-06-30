<?php

namespace JordanPartridge\StravaClient\Concerns;

use JordanPartridge\StravaClient\Resources\ActivityResource;
use JordanPartridge\StravaClient\Resources\AthleteResource;
use JordanPartridge\StravaClient\Resources\WebhookResource;

/**
 * Resource Methods Trait
 *
 * Provides the modern resource-based API for Strava integration.
 * Resources are lazy-loaded and cached for optimal performance.
 */
trait ResourceMethods
{
    /**
     * Cache for instantiated resources.
     *
     * @var array<string, object>
     */
    private array $resources = [];

    /**
     * Access activity-related operations.
     */
    public function activities(): ActivityResource
    {
        return $this->resources['activities'] ??= new ActivityResource(
            $this->strava,
            $this->max_refresh_attempts
        );
    }

    /**
     * Access athlete-related operations.
     */
    public function athlete(): AthleteResource
    {
        return $this->resources['athlete'] ??= new AthleteResource(
            $this->strava,
            $this->max_refresh_attempts
        );
    }

    /**
     * Access webhook-related operations.
     */
    public function webhooks(): WebhookResource
    {
        return $this->resources['webhooks'] ??= new WebhookResource(
            $this->strava,
            $this->max_refresh_attempts
        );
    }

    /**
     * Set authentication tokens for all resources.
     */
    public function withTokens(string $accessToken, string $refreshToken): self
    {
        $this->setToken($accessToken, $refreshToken);

        // Update tokens for any already-instantiated resources
        foreach ($this->resources as $resource) {
            $resource->withTokens($accessToken, $refreshToken);
        }

        return $this;
    }

    /**
     * Clear the resource cache.
     *
     * Useful when changing authentication context.
     */
    public function clearResourceCache(): self
    {
        $this->resources = [];

        return $this;
    }
}
