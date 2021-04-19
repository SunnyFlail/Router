<?php

namespace SunnyFlail\Router;

use \InvalidArgumentException;

class Route
{

    /**
     * String containing the name of Route
     *
     * @var string
     */
    protected string $name;

    /**
     * Array containing names of Http methods supported by this Route
     * 
     * @var array
     */
    protected array $methods;
    
    /**
     * String specifing the route path
     *
     * @var string
     */
    protected string $path;

    /**
     * Callback function used by this route
     *
     * @var callable
     */
    protected $callback;

    /**
     * Array of regex'es for $path params
     * eg. $path = "/index/{page}" 
     *     $params = ["page" => "/\d+/"]
     *
     * @var array|null
     */
    protected ?array $params;

    /**
     * Array containing default values for parameters
     * 
     * @var array|null
     */
    protected ?array $defaults;

    function __construct(
        string $name,
        string $path,
        array $methods,
        callable $callback,
        ?array $params = null,
        ?array $defaults = null
    ) {
        $this->name = $name;       
        $this->path = $path;       
        $this->callback = $callback;
        $this->methods = $methods;
        $this->params = $params;
        $this->defaults = $defaults;
    }

    /**
     * Returns name of Route
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns Callback
     *
     * @return callable
    */
    public function getCallback(): callable
    {
        return $this->callback;
    }
    
    /**
     * Checks if route is parametrised
     * 
     * @return bool
    */
    public function hasParams(): bool
    {
        return $this->params != null;
    }
    
    /**
     * Returns array containing param regexes | null
     *
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * Checks if route's parameters have default values
     *
     * @return bool
     */
    public function hasDefaults(): bool
    {
        return $this->defaults != null;
    }

    /**
     * Returns array containing default param values | null
     *
     * @return array|null
     */
    public function getDefaults(): ?array
    {
        return $this->defaults;
    }

    /**
     * Generates Url from provided data array
     * Data array MUST contain ALL params specified for the route
     *
     * @param array $data
     * 
     * @return string
     * @throws RoutingException
     */
    public function generateUrl(array $data = []): string
    {
        $url = $this->path;

        foreach ($data as $key => $value) {
            if ($key === "queries" || $key === "fragment") {
                continue;
            }
            if (!isset($this->params[$key])) { 
                throw new RoutingException(
                    sprintf("Provided data for unknown key '%s' !", $key)
                );
            }
            if (preg_match($this->params[$key], $value)) {
                $url = str_replace("{".$key."}", $value, $url);
            } else {
                throw new RoutingException(
                    sprintf("Data provided for key '%s' doesn't match the requirements!", $key)
                );
            }
        }
       
        if (isset($data["queries"])) {
            $url .= "?";

            foreach ($data["queries"] as $key => $value) {
                
                $url .= "$key=$value";

                unset($data["queries"][$key]);

                if (count($data["queries"] > 0)) {
                    $url .= "&";
                }
            }
        }

        if (isset($data["fragment"])) {
            $url .= "#".$data["fragment"];
        }

        if (preg_match_all("/({)(\w+)(})/i", $url, $inssufficient)) {
            throw new RoutingException(
                sprintf("Data not provided for keys %s !", implode(", ", $inssufficient[2]))
            );
        }
        
        return urlencode($url);
    }

    /**
     * Matches Route's path against provided url, returns Array with matched param data
     * 
     * @param string $method <- HTTP method
     * @param string $url <- URL to match against
     * 
     * @return MatchedRoute|null 
     */
    public function match(string $method, string $url): ?MatchedRoute
    {

        $data = [];

        if (!($this->params === null && $url === $this->path)) {
        
            $urlSegments = explode("/", $url);
            $routeSegments = explode("/", $this->path);

            for ($i = 0; $i < count($routeSegments); $i++) {

                if (isset($urlSegments[$i])) {

                    $currentUrlSegment = $urlSegments[$i];
                    $currentRouteSegment = $routeSegments[$i];

                    if (strcasecmp($currentUrlSegment, $currentRouteSegment) === 0) {
                        continue;
                    } 
                    
                    if (preg_match("/^({)(\w+)(})$/i", $currentRouteSegment, $paramName)) {

                        $paramName = $paramName[2];

                        if (!isset($this->params[$paramName])) {
                            throw new RoutingException(
                                sprintf("Regex for param '%s' not provided!"), $paramName
                            );
                        }
                        if (preg_match("/".$this->params[$paramName]."/", $currentUrlSegment, $paramData)) {
                            $data[$paramName] = $paramData[0];
                            continue;
                        }
                    }
                }
                
                if (empty($urlSegments[$i]) && $this->defaults !== null) {

                    if (!preg_match("/^({)(\w+)(})$/i", $routeSegments[$i], $paramName)) {
                        throw new RoutingException("Provided default for unknown param!");
                    }

                    $paramName = $paramName[2];

                    $data[$paramName] = $this->defaults[$paramName];
                    break;
                }

                return null;
            }
        }
        
        return new MatchedRoute($this, $data);
    }

    /**
     * Returns an array representation of this Route
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            "name" => $this->name,
            "path"=> $this->path,
            "methods"=> $this->methods,
            "callback" => is_array($this->callback) || is_string($this->callback) ? $this->callback : null,
            "params" => $this->params,
            "defaults" => $this->defaults
        ];
    }

}