<?php

namespace SunnyFlail\Router\Tests;

use \SunnyFlail\Router\{
    Router,
    Exceptions\NotFoundException,
    Exceptions\RoutingException
};

final class RouterProvider
{
    public static ?Router $router = null;

    public static function setUpRouter(): Router
    {
        if (!isset(self::$router)) {
            self::$router = new Router();

            foreach(RouteProvider::routeDataProvider() as $routeData) {
                self::$router->addRoute(...$routeData);
            }
        }

        return self::$router;
    }

    public static function matchProvider(): array
    {
        $router = self::setUpRouter();
        
        return [
            "Simple GET" => [
                $router, '/index', "GET", $router->getRoute("index")
            ],
            "Simple POST" => [
                $router, '/add', "POST", $router->getRoute("add")
            ],
            "GET with Params" => [
                $router, '/1', "GET", $router->getRoute("page") 
            ],
            "GET with Default" => [
                $router, "/", "GET", $router->getRoute("page")
            ]
        ];
    }

    public static function paramDataProvider(): array
    {
        $router = self::setUpRouter();

        return [
            "GET with Params" => [
                $router, '/1', "GET", ["page" => 1] 
            ],
            "GET with Default" => [
                $router, "/", "GET", ["page" => 0]
            ],
            "GET with multiple Params" => [
                $router, "/user/123", "GET", ["user_name" => 'user', "post_id" => '123']
            ]
        ];
    }

    public static function exceptionProvider(): array
    {
        $router = self::setUpRouter();

        return [
            "No Route Found" => [
                $router,
                fn(Router $router) => $router->match("GET", "/null/dull/"),
                NotFoundException::class
            ],
            "Route already exists!" => [
                $router,
                fn(Router $router) => $router->addRoute(
                    ...RouteProvider::routeDataProvider()["Simple post route"]
                ),
                RoutingException::class
            ]
        ];
    }

}