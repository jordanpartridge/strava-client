<?php

namespace JordanPartridge\StravaClient\Resources;

use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

/**
 * Activity Resource
 * 
 * Provides a clean, type-safe interface for activity-related operations.
 */
class ActivityResource extends BaseResource
{
    /**
     * List activities for the authenticated athlete.
     *
     * @param array $params Query parameters (page, per_page, before, after)
     * @return array Current implementation returns array - will be ActivityCollection in future
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function list(array $params = []): array
    {
        return $this->handleRequest(
            fn () => $this->connector->activityForAthlete($params)
        );
    }

    /**
     * Get a specific activity by ID.
     *
     * @param int $id The activity ID
     * @return array Current implementation returns array - will be ActivityData in future
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    public function get(int $id): array
    {
        return $this->handleRequest(
            fn () => $this->connector->getActivity($id)
        );
    }

    // TODO: Future methods to implement:
    // - update(int $id, array $data): ActivityData
    // - delete(int $id): bool
    // - getKudos(int $id): array
    // - getComments(int $id): array
    // - getStreams(int $id, array $keys): array
}