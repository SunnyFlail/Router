<?php

namespace SunnyFlail\Router\Interfaces;

use SunnyFlail\Router\Exceptions\NotFoundException;
use SunnyFlail\Router\Exceptions\RoutingException;
use SunnyFlail\Router\Exceptions\RouteOverwriteException;

interface IRouter
{

    /**
     * Adds new Route 
     *
     * @param string $name Name of the Route
     * @param string $path Path This Route will match
     * @param array|callable $callback Array containing Controller name and method name or callable
     * @param array $methods Http methods which this Route will respond to
     * @param array|null $params
     * @param array|null $defaults
     * 
     * @return IRouter
     */
    public function addRoute(
        string $name,
        string $path,
        array|callable $callback,
        array $methods = ['GET', 'HEAD'],
        array $params = [],
        array $defaults = []
    );

    /**
     * Checks if there is a Route registered with provided name
     * 
     * @param string $name
     * 
     * @return bool
     */
    public function hasRoute(string $name): bool;

    /**
     * Returns Route registered with provided name
     * 
     * @param string $name - name of searched Route
     * 
     * @return IRoute
     * 
     * @throws NotFoundException if there is no Route registered with provided name 
     */
    public function getRoute(string $name): IRoute;

    /**
     * Returns all registered routes
     * 
     * @return IRoute[]
     */
    public function getAllRoutes(): array;

    /**
     * Matches Request against Route
     * 
     * If a Route matches the url returns a MatchedData object,
     * containing data scraped from params and matched Route
     * 
     * @param string $method - name of HTTP method 
     * @param string $url Url of request
     * 
     * @return IMatchedData Object containing matched Route and data scraped from its params
     * 
     * @throws RoutingException if provided Route is malformed
     *                          (contains default value for non existing param)
     * @throws NotFoundException if no Route is matched
     */
    public function match(string $method, $requestPath): IMatchedData;

    /**
     * Adds provided Routes to collection
     * 
     * @param IRoute[] $routes
     * 
     * @return IRouter
     * @throws RouteOverwriteException if Route with provided name already exists
     */
    public function addRoutes(IRoute ...$routes);

    /**
     * Inserts routes from config array
     * 
     * @param array[] $config
     * 
     * @return IRouter
     * @throws RouteOverwriteException if Route with provided name already exists
     */
    public function insertConfig(array $config): IRouter;

}