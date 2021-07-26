<?php

namespace SunnyFlail\Router\Interfaces;

/**
 * Interface for RouteMatchers
 */
interface IRouteMatcher
{

    /**
     * Matches routes against request parameters
     * 
     * @param IRoute[] $routeContainer
     * @param string $requestMethod
     * @param string $requestUri
     * 
     * @return IMatchedData|null
     */
    public function matchRoute(array $routeContainer, string $requestMethod, string $requestUri): ?IMatchedData;

}