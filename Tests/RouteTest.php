<?php

namespace SunnyFlail\RouterTests;

use \SunnyFlail\Router\{
    Route,
    Exceptions\UrlGenerationException
};
use \PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    /**
     * @dataProvider \SunnyFlail\RouterTests\RouteDataProvider::generateUrlProvider
     */
    public function testGeneratingUrl(Route $route, array $data, string $expectedUrl)
    {
        $url = $route->generateUrl($data);
        $this->assertEquals($expectedUrl, $url);
    }

}