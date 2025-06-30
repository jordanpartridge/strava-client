<?php

namespace JordanPartridge\StravaClient\Resources;

use InvalidArgumentException;
use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\Exceptions\Authentication\MaxAttemptsException;
use JordanPartridge\StravaClient\Exceptions\Request\BadRequestException;
use JordanPartridge\StravaClient\Exceptions\Request\RateLimitExceededException;
use JordanPartridge\StravaClient\Exceptions\Request\ResourceNotFoundException;
use JordanPartridge\StravaClient\Exceptions\Request\StravaServiceException;
use JsonException;
use RuntimeException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

/**
 * Base Resource Class
 * 
 * Provides shared functionality for all Strava API resources including
 * error handling, request processing, and token management.
 */
abstract class BaseResource
{
    private const HTTP_UNAUTHORIZED = 401;
    private const HTTP_NOT_FOUND = 404;
    private const HTTP_BAD_REQUEST = 400;
    private const HTTP_RATE_LIMIT = 429;
    private const HTTP_SERVICE_UNAVAILABLE = 503;
    private const MAX_RETRY_ATTEMPTS = 3;
    private const BASE_RETRY_DELAY = 1000; // 1 second in milliseconds

    private int $currentAttempts = 0;

    public function __construct(
        protected Connector $connector,
        private int $maxRefreshAttempts = 3
    ) {
        if ($this->maxRefreshAttempts < 1) {
            throw new InvalidArgumentException('Max refresh attempts must be greater than 0.');
        }
    }

    /**
     * Handle API requests with comprehensive error handling and token refresh logic.
     *
     * @param callable $request
     * @return array
     * @throws BadRequestException
     * @throws FatalRequestException
     * @throws JsonException
     * @throws MaxAttemptsException
     * @throws RateLimitExceededException
     * @throws RequestException
     * @throws ResourceNotFoundException
     * @throws StravaServiceException
     */
    protected function handleRequest(callable $request): array
    {
        $response = $request();

        if ($response->successful()) {
            $this->currentAttempts = 0;
            return $response->json();
        }

        return match ($response->status()) {
            self::HTTP_UNAUTHORIZED => $this->handleUnauthorized($request),
            self::HTTP_BAD_REQUEST => throw new BadRequestException($response),
            self::HTTP_NOT_FOUND => throw new ResourceNotFoundException($response),
            self::HTTP_RATE_LIMIT => throw new RateLimitExceededException($response),
            self::HTTP_SERVICE_UNAVAILABLE => $this->handleServiceUnavailable($request),
            default => throw new StravaServiceException($response),
        };
    }

    /**
     * Handle unauthorized responses by refreshing tokens and retrying.
     *
     * @param callable $request
     * @return array
     * @throws FatalRequestException
     * @throws JsonException
     * @throws MaxAttemptsException
     * @throws RequestException
     * @throws BadRequestException
     * @throws RateLimitExceededException
     * @throws ResourceNotFoundException
     * @throws StravaServiceException
     */
    private function handleUnauthorized(callable $request): array
    {
        $this->currentAttempts++;
        
        if ($this->currentAttempts >= $this->maxRefreshAttempts) {
            throw new MaxAttemptsException('Maximum token refresh attempts exceeded', 403);
        }
        
        $response = $this->handleRequest(fn () => $this->connector->refreshToken());

        // Update tokens after successful refresh
        $this->connector->setToken(
            $response['access_token'],
            $response['refresh_token']
        );

        return $this->handleRequest($request);
    }

    /**
     * Handle service unavailable response with exponential backoff retry.
     *
     * @param callable $request
     * @param int $attempt
     * @return array
     * @throws RuntimeException
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
            throw new RuntimeException("Strava service unavailable after {$attempt} attempts", 503);
        }

        // Exponential backoff: 1s, 2s, 4s
        $delay = self::BASE_RETRY_DELAY * pow(2, $attempt - 1);
        usleep($delay * 1000); // convert to microseconds

        // Call the request directly to avoid handleRequest's match statement
        $response = $request();

        if (!$response->failed()) {
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
     * Set authentication tokens for the connector.
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @return self
     */
    public function withTokens(string $accessToken, string $refreshToken): self
    {
        $this->connector->setToken($accessToken, $refreshToken);
        
        return $this;
    }
}