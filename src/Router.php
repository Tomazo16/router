<?php 

namespace Tomazo\Router;

use Exception;
use Tomazo\Router\RouteLoader\RouteLoaderInterface;
use Tomazo\Router\RouteResolver\RouteResolverInterface;

class Router
{
    private $routes = [];

    public function __construct(private RouteLoaderInterface $routeLoader, private RouteResolverInterface $routeResolver)
    {
        try{
            $this->routes = $routeLoader->loadRoute();
        }catch(Exception $e){
            
        }
        
    }

    public function getRoutes(): array
    {
        $routes = [];

        foreach($this->routes as $route) {
            $routes[] = $this->routeResolver->resolveRoute($route);
        }
        return $routes;
    }

    public function getRoutePaths(): array
    {
        $routePaths = [];

        foreach($this->routes as $route) {
            $routePaths[] = $this->routeResolver->getRoutePaths($route);
        }

        return $routePaths;
    }
}