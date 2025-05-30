<?php 

namespace Tomazo\Router\RouteResolver;

use Exception;
use Tomazo\Router\Model\Route;
use Tomazo\Router\RouteResolver\RouteResolverInterface;
use Tomazo\Router\Utilities\RouteMatcher;

class SimpleRouteResolver implements RouteResolverInterface
{
    public function resolveRoute(Route $route): array
    {
        return [
            'name' => $route->getName(),
            'method' => $route->getMethod(),
            'route' => $route->getRoute(),
            'action' => $route->getAction()
        ];
    }

    public function callAction(string $path, Route $route): mixed
    {
        $method = new \ReflectionMethod($route->getAction()[0], $route->getAction()[1]);
        $routeMatcher = new RouteMatcher($path, $method, $route->getRoute());

        if ($routeMatcher->match() && !$routeMatcher->matchMethods($route->getMethod())) {
            throw new \RuntimeException("[code: 405] Method {$_SERVER['REQUEST_METHOD']} is not allowed in route {$route->getName()}",405);
        }

        return $routeMatcher->match() ? $routeMatcher->execute() : false;
    }

    public function getRoutePaths(Route $route): string
    {
        $methods = implode(' ', $route->getMethod() ?? []);
        return trim("{$methods} : {$route->getRoute()} | name : {$route->getName()}");
    }
}