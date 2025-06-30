<?php

namespace JordanPartridge\StravaClient\Contracts;

/**
 * Combined Strava Client Interface
 * 
 * Provides both legacy and modern resource-based APIs during the transition period.
 * This allows existing code to continue working while new code can use the modern API.
 */
interface StravaClientInterface extends LegacyStravaClientInterface, StravaResourceClientInterface
{
    //
}