<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\Router\RouteLoader\ControllerRouteLoader;
use Tomazo\Router\RouteResolver\SimpleRouteResolver;
use Tomazo\Router\Router;

class RouterIntegrationTest extends TestCase
{
    /**
     * Test verifies that the ControllerRouteLoader loads exactly 6 routes 
     * from the specified namespace and directory path for controllers.
     */
    public function testControllerRouteLoader(): void
    {
        // Initialize the ControllerRouteLoader with the namespace and path to controllers
        $controllerRouteLoader = new ControllerRouteLoader(
            'Tomazo\TestRouter\ControllersLoad', 
            __DIR__ . '/../ControllersLoad'
        );

         // Initialize the Router with the route loader and a simple route resolver
         $router = new Router($controllerRouteLoader, new SimpleRouteResolver());

       // Retrieve all routes
       $routes = $router->getRoutes();

       // Assert that there are 6 routes loaded
       $this->assertCount(6, $routes, "Expected 6 routes to be loaded.");
    }
}