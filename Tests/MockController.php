<?php

namespace SunnyFlail\RouterTests;

class MockController
{

    public function call()
    {
        echo self::class . "::call called!";
    }

    public function index()
    {
        echo self::class."::index called!";
    }

}