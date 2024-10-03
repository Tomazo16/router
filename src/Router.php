<?php 

namespace Tomazo\Router;

use Tomazo\Router\RouteLoader\RouteLoaderInterface;
use Tomazo\Router\RouteResolver\RouteResolverInterface;

class Router
{
    private $routes = [];

    public function __construct(private RouteLoaderInterface $routeLoader, private RouteResolverInterface $routeResolver)
    {
        $this->routes = $routeLoader->loadRoute();
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}