<?php

namespace SunnyFlail\Router\Interfaces;

/**
 * Interface for matched route wrappers
 */
interface IMatchedData
{
    /**
     * Returns data scraped from route parameters
     * 
     * It may be an empty array for Routes without parameters, 
     * Or an associative array
     * 
     * @return array Data scraped from Route 
     */
    public function getData(): array;

    /**
     * Returns the Route which was matched
     * 
     * @return IRoute
     */
    public function getRoute(): IRoute;

}