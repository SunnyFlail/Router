<?php

$strings = [

];

function testPreg(string $string): ?string
{
    if (preg_match("/^\{(\w+)\}$/i", $string, $paramName)) {
    
        return $paramName[1];
    }
    return null;
}

function testStr(string $string): ?string
{

}