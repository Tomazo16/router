<?php 

namespace Tomazo\Router\Helpers;

use Tomazo\Router\Exceptions\NoRoutesException;
use Tomazo\Router\Exceptions\RouteDuplicationException;

class RouteValidator
{
    public static function checkNoRoutes(array $routes, string $namespace, string $direction): void
    {
        if(empty($routes)) {
            throw new NoRoutesException($namespace, $direction);
        }
    }
    public static function checkIsRouteExist(array $routes, string $name, string $path, string $methodName): void
    {
        foreach($routes as $route) {
            if($route->getName() === $name || $route->getRoute() === $path) {
                throw new RouteDuplicationException($name,$route->getRoute(), $methodName);
            }
        }
    } 
}