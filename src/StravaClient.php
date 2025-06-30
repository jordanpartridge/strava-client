<?php

namespace JordanPartridge\StravaClient;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Exceptions\Authentication\MaxAttemptsException;
use JordanPartridge\StravaClient\Exceptions\Request\BadRequestException;
use JordanPartridge\StravaClient\Exceptions\Request\RateLimitExceededException;
use JordanPartridge\StravaClient\Exceptions\Request\ResourceNotFoundException;
use JordanPartridge\StravaClient\Exceptions\Request\StravaServiceException;
use JordanPartridge\StravaClient\Data\Webhooks\SubscriptionData;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

final class StravaClient
{
    private const HTTP_UNAUTHORIZED = 401;

    private const HTTP_NOT_FOUND = 404;

    private const HTTP_BAD_REQUEST = 400;

    private const HTTP_RATE_LIMIT = 429;

    private const HTTP_SERVICE_UNAVAILABLE = 503;

    private const MAX_RETRY_ATTEMPTS = 3;

    private const BASE_RETRY_DELAY = 1000; // 1 second in milliseconds

    private int $current_attempts;

    public function __construct(private Connector $strava, private int $max_refresh_attempts = 3)
    {
        $this->current_attempts = 0;

        if ($this->max_refresh_attempts < 1) {
            throw new InvalidArgumentException('Max refresh attempts must be greater than 0.');
        }
    }

    /**
     * Exchange authorization code for tokens
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function exchangeToken(string $code, string $grant_type = 'authorization_code'): array
    {
        return $this->handleRequest(
            fn () => $this->strava->exchangeToken($code, $grant_type)
        );
    }

    /**
     * Set access and refresh tokens
     */
    public function setToken(string $access_token, string $refresh_token): void
    {
        if (! $this->shouldSetToken($access_token, $refresh_token)) {
            throw new InvalidArgumentException('Access and refresh tokens must be set');
        }

        $this->strava->setToken($access_token, $refresh_token);
    }

    private function shouldSetToken(string $access_token, string $refresh_token): bool
    {
        return ! empty($access_token) && ! empty($refresh_token);
    }

    /**
     * Get activities for authenticated athlete
     *
     * @throws RequestException|FatalRequestException|JsonException
     */
    public function activityForAthlete(int $page, int $per_page): array
    {
        return $page < 1 || $per_page < 1
            ? throw new InvalidArgumentException('Page and per_page must be positive integers')
            : $this->handleRequest(fn () => $this->strava->activityForAthlete([
                'page' => $page,
                'per_page' => $per_page,
            ]));
    }

    /**
     * Get a specific activity by ID
     *
     *
     * @throws BadRequestException
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RateLimitExceededException
     * @throws MaxAttemptsException
     * @throws RequestException
     * @throws ResourceNotFoundException
     * @throws StravaServiceException
     */
    public function getActivity(int $id): array
    {
        return $id < 1
            ?
            throw new InvalidArgumentException('Activity ID must be positive')
            :
            $this->handleRequest(fn () => $this->strava->getActivity($id));
    }

    /**
     * Handle API request with automatic token refresh
     *
     * @throws BadRequestException
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RateLimitExceededException
     * @throws MaxAttemptsException
     * @throws RequestException
     * @throws ResourceNotFoundException
     * @throws StravaServiceException
     */
    private function handleRequest(callable $request): array
    {
        $response = $request();

        if (! $response->failed()) {
            $this->current_attempts = 0;

            return $response->json();
        }

        return match ($response->status()) {
            self::HTTP_UNAUTHORIZED => $this->handleUnauthorized($request),
            self::HTTP_NOT_FOUND => throw new ResourceNotFoundException($response),
            self::HTTP_BAD_REQUEST => throw new BadRequestException($response),
            self::HTTP_RATE_LIMIT => throw new RateLimitExceededException($response),
            self::HTTP_SERVICE_UNAVAILABLE => $this->handleServiceUnavailable($request),
            500, 502, 504 => throw new StravaServiceException($response),
            default => throw new RequestException($response),
        };
    }

    /**
     * Handle unauthorized response by refreshing token and retrying
     *
     *
     * @throws FatalRequestException
     * @throws JsonException
     * @throws MaxAttemptsException
     * @throws RequestException
     */
    private function handleUnauthorized(callable $request): array
    {
        $this->current_attempts++;
        if ($this->current_attempts >= $this->max_refresh_attempts) {
            throw new MaxAttemptsException('Maximum token refresh attempts exceeded', 403);
        }
        $response = $this->handleRequest(fn () => $this->strava->refreshToken());

        // Update tokens after successful refresh
        $this->strava->setToken(
            $response['access_token'],
            $response['refresh_token']
        );

        return $this->handleRequest($request);
    }

    /**
     * Handle service unavailable response with exponential backoff retry
     *
     * @throws StravaServiceException
     * @throws BadRequestException
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RateLimitExceededException
     * @throws MaxAttemptsException
     * @throws RequestException
     * @throws ResourceNotFoundException
     */
    private function handleServiceUnavailable(callable $request, int $attempt = 1): array
    {
        if ($attempt > self::MAX_RETRY_ATTEMPTS) {
            throw new \RuntimeException("Strava service unavailable after {$attempt} attempts", 503);
        }

        // Exponential backoff: 1s, 2s, 4s
        $delay = self::BASE_RETRY_DELAY * pow(2, $attempt - 1);
        usleep($delay * 1000); // convert to microseconds

        // Call the request directly to avoid handleRequest's match statement
        $response = $request();

        if (! $response->failed()) {
            return $response->json();
        }

        // If it's still a 503, retry with incremented attempt
        if ($response->status() === self::HTTP_SERVICE_UNAVAILABLE) {
            return $this->handleServiceUnavailable($request, $attempt + 1);
        }

        // Otherwise, let handleRequest deal with other error types
        return $this->handleRequest($request);
    }

    /**
     * Create a webhook subscription for the authenticated application.
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function createWebhookSubscription(?string $callbackUrl = null, ?string $verifyToken = null): SubscriptionData
    {
        $callbackUrl ??= config('strava-client.webhook.callback_url');
        $verifyToken ??= config('strava-client.webhook.verify_token');

        if (empty($callbackUrl) || empty($verifyToken)) {
            throw new InvalidArgumentException('Callback URL and verify token are required for webhook subscription');
        }

        $response = $this->handleRequest(fn () => $this->strava->createWebhookSubscription($callbackUrl, $verifyToken));

        return SubscriptionData::fromArray($response);
    }

    /**
     * Delete a webhook subscription.
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function deleteWebhookSubscription(int $subscriptionId): bool
    {
        $this->handleRequest(fn () => $this->strava->deleteWebhookSubscription($subscriptionId));

        return true;
    }

    /**
     * View current webhook subscriptions.
     *
     * @return SubscriptionData[]
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function viewWebhookSubscriptions(): array
    {
        $response = $this->handleRequest(fn () => $this->strava->viewWebhookSubscriptions());

        return array_map(
            fn (array $subscription) => SubscriptionData::fromArray($subscription),
            $response
        );
    }
}
