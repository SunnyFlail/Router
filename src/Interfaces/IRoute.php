<?php

namespace SunnyFlail\Router\Interfaces;

use JsonSerializable;
use SunnyFlail\Router\Exceptions\UrlGenerationException;

interface IRoute extends JsonSerializable
{

    /**
     * Returns name of Route
     *
     * @return string 
     */
    public function getName(): string;

    /**
     * Returns routes path - as was provided by user
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Returns Callback associated with this Route
     *
     * @see $callback
     * 
     * @return array|callable
    */
    public function getCallback(): mixed;

    /**
     * Checks if route is parametrised
     * 
     * @return bool
    */
    public function hasParams(): bool;

    /**
     * Returns array containing params
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * Checks if this Route's parameters have default values
     *
     * @return bool
     */
    public function hasDefaults(): bool;

    /**
     * Returns array containing default param values | null
     *
     * @return array|null
     */
    public function getDefaults(): array;

    /**
     * Returns array containing HTTP methods this Route responds to
     *
     * @return array
     */
    public function getMethods(): array;

    /**
     * Generates Url from provided data array
     * 
     * Data array MUST contain ALL params specified for the route
     * 
     * For eg. Schema @see $params
     * 
     * @param array $data Data adhering to this Route's schema
     * 
     * @return string Generated Url pointing to this Route
     * @throws UrlGenerationException When provided data is malformed 
     */
    public function generateUrl(array $data = []): string;

}