<?php

namespace SunnyFlail\Router\Exceptions;

/**
 * Exception thrown when Route was malformed,
 * OR when you try to get a non-existing Route from Router
 * 
 * Exception mostly for debugging
 */
class RoutingException extends \Exception
{
}