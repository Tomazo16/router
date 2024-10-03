<?php 

namespace Tomazo\Router\RouteResolver;

use Tomazo\Router\RouteResolver\RouteResolverInterface;

class SimpleRouteResolver implements RouteResolverInterface
{
    public function resolveRoute(array $route): string
    {
        return $route['route'];
    }
}