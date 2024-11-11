<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\Router\Exceptions\NoControllersException;
use Tomazo\Router\Exceptions\NoRoutesException;
use Tomazo\Router\Exceptions\RouteDuplicationException;
use Tomazo\Router\RouteLoader\ControllerRouteLoader;
use Tomazo\TestRouter\Controllers\DuplicationController;
use Tomazo\TestRouter\Controllers\NoRouteController;
use Tomazo\TestRouter\RouteLoader\TestControllerRouteLoader;

class ControllerLoaderUnitTest extends TestCase
{
    private $testControllerRouteLoader;

    public function setUp(): void
    {
        $this->testControllerRouteLoader = new TestControllerRouteLoader('','');
    }

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

         
        $routes = $controllerRouteLoader->loadRoute();

       // Assert that there are 6 routes loaded
       $this->assertCount(6, $routes, "Expected 6 routes to be loaded.");
    }

     /**
     * Test that an exception is thrown when a route duplication is detected.
     */
    public function testDuplicationRouteThrowException(): void
    {
        $this->expectException(RouteDuplicationException::class);
        $this->expectExceptionMessage("Route duplication detected. Route: 'dupl' with path '/a' already exists (method: b).");
        $this->expectExceptionCode(500);

        $this->testControllerRouteLoader->registerController(DuplicationController::class);
        $this->testControllerRouteLoader->loadRoute();
    }

    /**
     * Test to ensure an exception is thrown if no controllers are available.
     */
    public function testNoControllersAvailable(): void
    {
        $this->expectException(NoControllersException::class);
        $this->expectExceptionMessage("No controllers detected in direction '' or namespace ''.");
        $this->expectExceptionCode(500);

        $this->testControllerRouteLoader->loadRoute();
    }

    /**
     * Test to ensure an exception is thrown if no routes are found in the controller.
     */
    public function testNoRoutesFound(): void
    {
        $this->expectException(NoRoutesException::class);
        $this->expectExceptionMessage("No routes detected in direction '' or namespace ''.");
        $this->expectExceptionCode(500);

        $this->testControllerRouteLoader->registerController(NoRouteController::class);
        $this->testControllerRouteLoader->loadRoute();
    }
}