<?php

namespace SunnyFlail\Router;

use SunnyFlail\Router\Exceptions\UrlGenerationException;
use SunnyFlail\Router\Interfaces\IRoute;

/**
 * A Route
 */
final class Route implements IRoute
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
     * eg. ["GET", "OPTIONS"]
     * 
     * @var array
     */
    protected array $methods;
    
    /**
     * String specifing the path route will match
     *
     * @var string
     */
    protected string $path;

    /**
     * Callback function used by this route
     *
     * This may be an array containing the Controller fqcn and method name
     * 
     * @var array|callable
     */
    protected $callback;

    /**
     * Array of regex'es for $path params
     * 
     * eg. $path = "/index/{page}" 
     *     $params = ["page" => "\d+"]
     *
     * @var array
     */
    protected array $params;

    /**
     * Array containing default values for parameters
     * 
     * @var array
     */
    protected array $defaults;

    function __construct(
        string $name,
        string $path,
        array|callable $callback,
        array $methods = ["GET", "HEAD"],
        array $params = [],
        array $defaults = []
    ) {
        $this->name = $name;       
        $this->path = $path;       
        $this->callback = $callback;
        $this->methods = $methods;
        $this->params = $params;
        $this->defaults = $defaults;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCallback(): mixed
    {
        return $this->callback;
    }

    public function hasParams(): bool
    {
        return $this->params != null;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasDefaults(): bool
    {
        return $this->defaults != null;
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function generateUrl(array $data = []): string
    {
        $url = $this->path;
        $matchedParams = [];

        if (is_null($this->params)) {
            return $url;
        }
        
        foreach ($this->params as $paramName => $paramRegex) {
            if (!isset($data[$paramName]) && !isset($this->defaults[$paramName])) {
                throw new UrlGenerationException(
                    sprintf("Data not provided for key %s of Route %s!", $paramName, $this->name)
                );    
            }

            $matchedParams[] = $paramName;

            if (isset($data[$paramName])) {
                $paramData = $data[$paramName];
                if (!preg_match("/$paramRegex/i", $paramData)) {
                    throw new UrlGenerationException(
                        sprintf(
                            "Data provided for param %s of Route %s doesn't match the requirements!",
                            $paramName, $this->name
                        )
                    );
                }
                $paramData = urlencode($paramData);
                $url = str_replace("{".$paramName."}", $paramData, $url);
                continue;
            }

            $url = substr($url, 0, strpos($url, "{$paramName}") - 1);
            break;
        }

        if ($insufficient = array_diff(
            array_diff(array_keys($this->params), array_keys($this->defaults)),
            $matchedParams
        )) {
            throw new UrlGenerationException(
                sprintf(
                    "Data wasn't provided for params '%s' of Route %s",
                    implode(", ", $insufficient)
                )
            );
        }

        return $url;
    }

    public function jsonSerialize(): array
    {
        return [
            "name" => $this->name,
            "path"=> $this->path,
            "methods"=> $this->methods,
            "callback" => (is_array($this->callback) || is_string($this->callback))
            ? $this->callback : "Closure",
            "params" => $this->params,
            "defaults" => $this->defaults
        ];
    }

}