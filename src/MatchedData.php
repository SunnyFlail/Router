<?php

namespace SunnyFlail\Router;

use SunnyFlail\Router\Interfaces\IMatchedData;
use SunnyFlail\Router\Interfaces\IRoute;

/**
 * Class containing matched Route and array containing data from route params
 */
final class MatchedData implements IMatchedData
{
    /**
     * @var Route Route from which this data was scraped from
     */
    private IRoute $route;

    /**
     * @var array Data matched from Route params
     * 
     * For schema @see \SunnyFlail\Router\Route::
     */
    private array $data;

    public function __construct(IRoute $route, array $data)
    {
        $this->route = $route;
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRoute(): IRoute
    {
        return $this->route;
    }

}