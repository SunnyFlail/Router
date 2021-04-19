<?php

namespace SunnyFlail\Router;

class MatchedRoute
{

    /**
     * Matched Route data
     * @var array
     */
    protected array $matchedData;

    /**
     * Route which is matched
     * @var Route
     */
    protected Route $route;

    public function __construct(Route $route, array $data = [])
    {
        $this->route = $route;
        $this->matchedData = $data;
    }

    /**
     * Returns Route registered name
     * @return string
     */
    public function getName(): string
    {
        return $this->route->getName();
    }

    /**
     * Returns array containing matched data
     * Schema:
     * [
     *    'paramName' => data
     * ]
     * @return array
     */
    public function getData(): array
    {
        return $this->matchedData;
    }

    /**
     * Returns path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->route->getPath();
    }

    /**
     * Returns Callback
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->route->getCallback();
    }
    
    /**
     * Checks if route is parametrised
     * 
     * @return bool
     */
    public function hasParams(): bool
    {
        return $this->route->hasParams();
    }
    
    /**
     * Returns array containing param regexes | null
     *
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->route->getParams();
    }

    /**
     * Checks if route's parameters have default values
     * @return bool
     */
    public function hasDefaults(): bool
    {
        return $this->route->hasDefaults();
    }

    /**
     * Returns array containing default param values | null
     *
     * @return array|null
     */
    public function getDefaults(): ?array
    {
        return $this->route->getDefaults();
    }

    /**
     * Generates Url from provided data array
     * Data array MUST contain ALL params specified for the route
     *
     * @param array $data
     * @return string
     */
    public function generateUrl(array $data = []): string
    {        
        return $this->route->generateUrl($data);
    }

    /**
     * Returns an array representation of this Route
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return $this->route->getConfig();
    }

    /**
     * Returns the matched Route object
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

}