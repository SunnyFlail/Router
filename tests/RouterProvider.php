<?php

namespace SunnyFlail\Router\Tests;

use \SunnyFlail\Router\{
    Router,
    Route,
    RoutingException
};

final class RouterProvider
{

    public static function setUpRouter(): Router
    {
        $router = new Router();

        $router->addRoute(
            name: 'add',
            path: "/add",
            callback: fn() => printf("Add!"),
            methods: ["POST", "HEAD"]
        );
        $router->addRoute(
            name: 'page',
            path: "/{page}",
            callback: fn() => printf("Page!"),
            params: [
                "page" => "\d+"
            ],
            defaults: [
                "page" => 0
            ]
        );
        $router->addRoute(
            name: 'user_post',
            path: "/{user_name}/{post_id}",
            callback: fn() => printf("User post!"),
            params: [
                "user_name" => "\w+",
                "post_id" => "\d+"
            ]
        );

        $router->addRoute(
            name: 'index',
            path: "/index",
            callback: fn() => printf("Index!")
        );

        return $router;
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
            ],
            "No Route Found" => [
                $router, "/null/", "GET", null
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

}