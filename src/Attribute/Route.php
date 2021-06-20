<?php

namespace SunnyFlail\Router\Attribute;

use \Attribute;
use \Exception;
use \ReflectionFunctionAbstract;
use \ReflectionMethod;
use \SunnyFlail\Router\Route as RouteObject;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    private string $name;
    private string $path;
    private array $methods;
    private ?array $params;
    private ?array $defaults;

    public function __construct(
        string $name,
        string $path,
        array $methods = ["GET", "HEAD"],
        ?array $params = null,
        ?array $defaults = null 
    ) {
        $this->name = $name;
        $this->path = $path;
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

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getDefaults(): ?array
    {
        return $this->defaults;
    }

    public function generateRoute(ReflectionFunctionAbstract $reflection): RouteObject
    {
        if ($reflection instanceof ReflectionMethod) {
            $callback = [
                $reflection->getDeclaringClass()->getName(),
                $reflection->getName()
            ];
        } else
        {
            $callback = $reflection->getName();
        }

        return new RouteObject(
            $this->name,
            $this->path,      
            $callback,
            $this->methods,
            $this->params,
            $this->defaults
        );
    }

}