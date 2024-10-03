<?php 

namespace Tomazo\Router\Helpers;

use Tomazo\Router\Exceptions\NoRoutesException;
use Tomazo\Router\Exceptions\RouteDuplicationException;

class RouteValidator
{
    public static function checkNoRoutes(array $routes, string $direction): void
    {
        if(empty($routes)) {
            throw new NoRoutesException($direction);
        }
    }
    public static function checkIsRouteExist(array $routes, string $name, string $path, string $methodName): void
    {
        foreach($routes as $route) {
            if($route['name'] === $name || $route['route'] === $path) {
                throw new RouteDuplicationException($name,$route['route'], $methodName);
            }
        }
    } 
}