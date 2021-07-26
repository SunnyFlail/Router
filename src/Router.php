<?php

namespace SunnyFlail\Router;

use SunnyFlail\Router\Exceptions\NotFoundException;
use SunnyFlail\Router\Exceptions\RouteOverwriteException;
use SunnyFlail\Router\Exceptions\RoutingException;
use SunnyFlail\Router\Interfaces\IMatchedData;
use SunnyFlail\Router\Interfaces\IRoute;
use SunnyFlail\Router\Interfaces\IRouter;

/**
 * Class which is responsible for storing Routes and Matching Request against them
 */
final class Router implements IRouter
{

    /**
     * Array containing registered routes
     * 
     * @var array<IRoute>
     */
    private array $routeCollection;

    public function __construct()
    {
        $this->routeCollection = [];
    }

    public function addRoute(
        string $name,
        string $path,
        array|callable $callback,
        array $methods = ['GET', 'HEAD'],
        array $params = [],
        array $defaults = []
    ) {
        if (isset($this->routeCollection[$name])) {
            throw new RouteOverwriteException($name);
        }
                
        $this->routeCollection[$name] = new Route($name, $path, $callback, $methods, $params, $defaults);

        return $this;
    }

    public function hasRoute(string $name): bool
    {
        return isset($this->routeCollection[$name]);
    }

    public function getRoute(string $name): IRoute
    {
        if (!isset($this->routeCollection[$name])) {
            throw new NotFoundException(
                sprintf('Route with name %s not found!', $name)
            );
        }
        
        return $this->routeCollection[$name];
    }

    public function getAllRoutes(): array
    {
        return $this->routeCollection;
    }

    public function match(string $method, $requestPath): IMatchedData
    {

        foreach ($this->routeCollection as $route) {

            $routePath = $route->getPath();
            $defaults = $route->getDefaults();
            $params = $route->getParams();

            $data = [];

            if (!in_array($method, $route->getMethods())) {
                continue;
            }
    
            if (!($params === [] && $requestPath === $routePath)) {
            
                $requestSegments = explode('/', $requestPath);
                $routeSegments = explode('/', $routePath);
    
                for ($i = 0; $i < count($routeSegments); $i++) {
    
                    $currentRequestSegment = $requestSegments[$i] ?? '';
                    $currentRouteSegment = $routeSegments[$i];
    
                    if (strcasecmp($currentRequestSegment, $currentRouteSegment) === 0) {
                        continue;
                    } 
                    
                    if (preg_match('/^\{(\w+)\}$/i', $currentRouteSegment, $paramName)) {

                        $paramName = $paramName[1];

                        if (!isset($params[$paramName])) {
                            throw new RoutingException(
                                sprintf('Regex for param %s not provided!', $paramName));
                        }
                        if (preg_match(
                                sprintf('/%s/', $params[$paramName]),
                                $currentRequestSegment,
                                $paramData)
                        ) {
                            $data[$paramName] = $paramData[0];
                            continue;
                        }
                        if (isset($defaults[$paramName])
                        && !isset($requestSegments[$i + 1])
                        ) {
                            $data[$paramName] = $defaults[$paramName];
                            break;
                        }
                    }
                    continue(2);
                }
            }
            
            return new MatchedData($route, $data); 
        }

        throw new NotFoundException(sprintf(
            'No route matches path %s', $requestPath));
    }

    public function addRoutes(IRoute ...$routes): IRouter
    {
        foreach($routes as $route) {
            $name = $route->getName();

            if (isset($this->routeCollection[$name])) {
                throw new RoutingException(
                    sprintf('Route with name %s has already been registered!!', $name)
                );
            }
            $this->routeCollection[$name] = $route;
        }

        return $this;
    }

    public function insertConfig(array $config): IRouter
    {

        foreach ($config as $config) {
            [$name] = $config;

            if (isset($this->routeCollection[$name])) {
                throw new RouteOverwriteException($name);
            }

            $this->routeCollection[$name] = new Route(...$config);
        }

        return $this;
    }

}