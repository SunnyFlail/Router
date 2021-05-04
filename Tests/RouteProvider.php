<?php

namespace SunnyFlail\Router\Tests;

use \SunnyFlail\Router\{
    Route,
    Exceptions\UrlGenerationException
};

final class RouteProvider
{
    
    public static function routeDataProvider(): array
    {
       return
       [
            "Simple post route" => [
                'add',
                "/add/",
                fn() => printf("Add!"),
                ["POST", "HEAD"]
            ],
            "Route with default Param" => [
                'page',
                "/{page}/",
                fn() => printf("Page!"),
                [
                    "GET", "HEAD"
                ],[
                    "page" => "\d+"
                ],
                [
                    "page" => 0
                ]
            ],
            "Route with multiple params" => [
                'user_post',
                "/{user_name}/{post_id}/",
                fn() => printf("User post!"),
                [
                    "GET", "HEAD"
                ],
                [
                    "user_name" => "\w+",
                    "post_id" => "\d+"
                ]
            ],
            "Route with multiple defaults" => [
                'entries_page_orderby',
                "/entries/{page}/{orderby}",
                fn() => printf("Index! but paged and ordered :)"),
                [
                    "GET", "HEAD"
                ],
                [
                    "page" => "\d+",
                    "orderby" => "\w+"
                ],
                [
                    "page" => 0,
                    "orderby" => "id_asc"
                ]
            ],
            "Simple get route" => [
                'index',
                "/index/",
                fn() => printf("Index!")    
            ]
        ];
    }

    private static array $routes;

    public static function routeProvider(): array
    {
        if (!isset(self::$routes)) {
            self::$routes = [];
            foreach (self::routeDataProvider() as $name => $data) {
                self::$routes[$name] = new Route(...$data);
            }
        }
        return self::$routes;
    }

    public static function generateUrlProvider()
    {
        return [
            "Simple Route" => [
                self::routeProvider()["Simple post route"],
                [],
                "/add/"
            ],
            "Route with param" => [
                self::routeProvider()["Route with default Param"],
                [
                    "page" => 123
                ],
                "/123/"
            ],
            "Route with default" => [
                self::routeProvider()["Route with default Param"],
                [],
                "/"
            ],
            "Route with multiple params" => [
                self::routeProvider()["Route with multiple params"],
                [
                    "user_name" => "papryczeros",
                    "post_id" => "123"
                ],
                "/papryczeros/123/",
            ],
            "Route with param and default" => [
                self::routeProvider()["Route with multiple defaults"],
                [
                    "page" => "123",
                ],
                "/entries/123/",
            ],
            "Route with multiple defaults" => [
                self::routeProvider()["Route with multiple defaults"],
                [
                ],
                "/entries/",
            ],
        ];
    }

    public static function exceptionProvider(): array
    {
        return [
            "No Route Found" => [

            ],
            "Route already exists!" => [

            ],
            "Unsupported method!" => [

            ]
        ];
    }

}