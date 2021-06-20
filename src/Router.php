<?php

namespace SunnyFlail\Router;

use SunnyFlail\Router\Exceptions\NotFoundException;
use SunnyFlail\Router\Exceptions\RoutingException;

/**
 * Class which is responsible for storing Routes and Matching Request against them
 */
class Router
{

    /**
     * Array containing registered routes
     * 
     * @var array<Route>
     */
    private array $routeCollection;

    public function __construct()
    {
        $this->routeCollection = [];
    }

    /**
     * Adds new Route 
     *
     * @param string $name Name of the Route
     * @param string $path Path This Route will match
     * @param array|callable $callback Array containing Controller name and method name or callable
     * @param array $methods Http methods which this Route will respond to
     * @param array|null $params
     * @param array|null $defaults
     * @return void
     */
    public function addRoute(
        string $name, string $path,  array|callable $callback,
        array $methods = ["GET", "HEAD"],
        array $params = [], array $defaults = []
    ){       
        if (isset($this->routeCollection[$name])) {
            throw new RoutingException(
                sprintf("Route with name %s has already been registered!!", $name)
            );
        }
                
        $this->routeCollection[$name] = new Route($name, $path, $callback, $methods, $params, $defaults);
    }

    /**
     * Returns Route registered with provided name
     * 
     * @param string $name - name of searched Route
     * @throws RoutingException if there is no Route registered with provided name 
     * @return Route
     */
    public function getRoute(string $name): Route
    {
        if (!isset($this->routeCollection[$name])) {
            throw new RoutingException(
                sprintf("Route with name '%s' not found!", $name)
            );
        }
        
        return $this->routeCollection[$name];
    }

    /**
     * Returns all registered routes
     * 
     * @return Route[]
     */
    public function getAllRoutes(): array
    {
        return $this->routeCollection;
    }

    /**
     * Matches Request against Route
     * 
     * If a Route matches the url returns a MatchedData object,
     * containing data scraped from params and matched Route
     * 
     * @param string $method - name of HTTP method 
     * @param string $url
     * @throws NotFoundException if no Route is matched
     * @throws RoutingException if provided Route is malformed
     *                          (contains default value for non existing param)
     * @return MatchedData Object containing matched Route and data scraped from its params
     */
    public function match(string $method, $requestPath): MatchedData
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
            
                $requestSegments = explode("/", $requestPath);
                $routeSegments = explode("/", $routePath);
    
                for ($i = 0; $i < count($routeSegments); $i++) {
    
                    $currentRequestSegment = $requestSegments[$i] ?? "";
                    $currentRouteSegment = $routeSegments[$i];
    
                    if (strcasecmp($currentRequestSegment, $currentRouteSegment) === 0) {
                        continue;
                    } 
                    
                    if (preg_match("/^\{(\w+)\}$/i", $currentRouteSegment, $paramName)) {

                        $paramName = $paramName[1];

                        if (!isset($params[$paramName])) {
                            throw new RoutingException(
                                sprintf("Regex for param '%s' not provided!", $paramName));
                        }
                        if (preg_match(
                                sprintf("/%s/", $params[$paramName]),
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
            "No route matches path '%s'", $requestPath));
    }

    /**
     * Adds provided Routes to collection
     */
    public function addRoutes(Route ...$routes)
    {
        foreach($routes as $route) {
            $name = $route->getName();

            if (isset($this->routeCollection[$name])) {
                throw new RoutingException(
                    sprintf("Route with name %s has already been registered!!", $name)
                );
            }
            $this->routeCollection[$name] = $route;
        }
    }

    /**
     * Inserts routes from config array
     * 
     * @param array[] $config
     */
    public function insertConfig(array $config)
    {
        $this->routeCollection = array_reduce(
            $config,
            function(array $carry, array $current) {
                $name = array_shift($current);
                $carry[$name] = new Route($name, ...$current);
                return $carry;
            },
            []
        );
    }

}