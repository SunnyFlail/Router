<?php

namespace SunnyFlail\Router\Tests;

use \SunnyFlail\Router\{
    Router,
    Route,
    RoutingException
};
use \PHPUnit\Framework\TestCase;

final class RoutingTest extends TestCase
{

    public function paramDataProvider()
    {
        return [
            "Simple GET" => [
                '/index', "GET", self::$router->get("index")
            ],
            "Simple POST" => [
                '/add', "POST", self::$router->get("add")
            ],
            "GET with Params" => [
                '/1', "GET", [
                    "page" => 1
                ] 
            ],
            "No Route Found" => [
                "/null/", "GET", null
            ]
        ];
    }

    /**
     * @dataProvider \SunnyFlail\Router\Tests\RouterProvider::matchProvider
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
     * @dataProvider \SunnyFlail\Router\Tests\RouterProvider::paramDataProvider
     */
    public function testParamMatching(Router $router, string $url, string $method, array $expected)
    {
        $matched = $router->match($method, $url)->getData();

        $this->assertEquals(
            $expected,
            $matched
        );
    }

}