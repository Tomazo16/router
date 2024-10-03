<?php 

namespace Tomazo\TestRouter\RouteLoader;

use Tomazo\Router\RouteLoader\ControllerRouteLoader;
use Tomazo\Router\Exceptions\NoControllersException;

class TestControllerRouteLoader extends ControllerRouteLoader
{
    private $controllers = [];

    public function __construct()
    {
        
    }

    public function registerController($controller): void
    {
        $this->controllers[] = $controller;
    }

    public function loadRoute(): array
    {

        foreach($this->controllers as $controller) {
            $this->registerRoutesForController(new $controller);
        }
        if(empty($this->controllers)) {
            throw new NoControllersException($this->controllersPath);
        }
        return $this->routes;
    }
}