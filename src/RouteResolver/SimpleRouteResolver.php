<?php 

namespace Tomazo\Router\RouteResolver;

use Tomazo\Router\Model\Route;
use Tomazo\Router\RouteResolver\RouteResolverInterface;

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

    public function callAction(Route $route): mixed
    {
        return '';
    }

    public function getRoutePaths(Route $route): string
    {
        $methods = implode(' ', $route->getMethod() ?? []);
        return trim("{$methods} : {$route->getRoute()} | name : {$route->getName()}");
    }
}