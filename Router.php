<?php

namespace SunnyFlail\Router;

class Router
{

    /**
     * Array containing registered routes
     * 
     * @var array
     */
    private array $routeCollection = [];

    /**
     * Array containing allowed HTTP methods
     * 
     * @var array
     */
    private const ALLOWED_METHODS = [
        "GET",
        "POST",
        "PUT",
        "HEAD",
        "OPTIONS",
        "DELETE"
    ];    

    /**
     * Adds new Route 
     *
     * @param array $methods
     * @param string $name
     * @param string $path
     * @param callable $callback
     * @param array|null $params
     * @param array|null $defaults
     * @return void
     */
    public function addRoute(
        string $name,
        string $path,
        $callback,
        array $methods = ["GET", "HEAD"],
        array $params = null,
        array $defaults = null
    ){       
        if ($notSupported = array_diff($methods, self::ALLOWED_METHODS)) {
            throw new RoutingException(
                sprintf("Methods `%s` aren't supported!", implode(" ,", $notSupported))
            );
        }

        if (isset($this->routeCollection[$name])) {
            throw new RoutingException(
                sprintf("Route with name %s has already been registered!!", $name)
            );
        }
                
        $this->routeCollection[$name] = new Route($name, $path, $methods, $callback, $params, $defaults);
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
     * Matches url against Route
     * If a Route matches the url, returns a MatchedRoute object containing data scraped from params
     * Returns null if no Route matched provided url and method
     * 
     * @param string $method - name of HTTP method 
     * @param string $url
     * @return MatchedRoute|null
     */
    public function match(string $method, string $url): ?MatchedRoute
    {
        $method = strtoupper($method);
        $url = $url[-1] === "/" ? $url : $url."/";

        foreach ($this->routeCollection as $route) {
            if (!is_null($matchedRoute = $route->match($method, $url))) {
                return $matchedRoute;
            }
        }

        return null;
    }

}