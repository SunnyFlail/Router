<?php

namespace SunnyFlail\Router\Exceptions;

use Exception;

class RouteOverwriteException extends Exception
{

    public function __construct(string $name)
    {
        parent::__construct(
            sprintf("Route with name %s has already been registered!!", $name)
        );
    }

}