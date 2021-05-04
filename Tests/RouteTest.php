<?php

namespace SunnyFlail\Router\Tests;

use \SunnyFlail\Router\{
    Route,
    Exceptions\UrlGenerationException
};
use \PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    /**
     * @dataProvider \SunnyFlail\Router\Tests\RouteProvider::generateUrlProvider
     */
    public function testGeneratingUrl(Route $route, array $data, string $expectedUrl)
    {
        $url = $route->generateUrl($data);
        $this->assertEquals($expectedUrl, $url);
    }

}