<?php

namespace SunnyFlail\RouterTests;

use \SunnyFlail\Router\{
    Router,
    Route
};
use \PHPUnit\Framework\TestCase;

final class RoutingTest extends TestCase
{

    /**
     * @dataProvider \SunnyFlail\RouterTests\RouterProvider::matchProvider
     */
    public function testRouteMatching(Router $router, string $url, string $method, ?Route $expected)
    {
        $matched = $router->match($method, $url)?->getRoute();

        $this->assertEquals(
            $expected,
            $matched
        );
    }

    /**
     * @dataProvider \SunnyFlail\RouterTests\RouterProvider::paramDataProvider
     */
    public function testParamMatching(Router $router, string $url, string $method, array $expected)
    {
        $matchedData = $router->match($method, $url)?->getData();

        $this->assertEquals(
            $expected,
            $matchedData
        );
    }

    /**
     * @dataProvider \SunnyFlail\RouterTests\RouterProvider::exceptionProvider
     */
    public function testExceptionThrowing(Router $router, callable $callback, string $exceptionName)
    {
        $this->expectException($exceptionName);

        call_user_func($callback, $router);
    }

}