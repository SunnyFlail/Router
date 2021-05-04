<?php

namespace SunnyFlail\Router;

/**
 * Class containing matched Route and array containing data from route params
 */
final class MatchedData
{
    /**
     * @var Route Route from which this data was scraped from
     */
    private Route $route;

    /**
     * @var array Data matched from Route params
     * 
     * For schema @see \SunnyFlail\Router\Route::
     */
    private array $data;

    public function __construct(Route $route, array $data)
    {
        $this->route = $route;
        $this->data = $data;
    }

    /**
     * Returns data scraped from route parameters
     * 
     * It may be an empty array for Routes without parameters, 
     * Or an associative array
     * 
     * @return array Data scraped from Route 
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the Route which was matched
     * 
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

}