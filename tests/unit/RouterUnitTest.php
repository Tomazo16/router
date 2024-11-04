<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\Router\Router;
use Tomazo\Router\Attribute\Route;
use Tomazo\Router\RouteResolver\SimpleRouteResolver;
use Tomazo\TestRouter\RouteLoader\TestControllerRouteLoader;
use Tomazo\TestRouter\Controllers\IndexController;
use Tomazo\TestRouter\Controllers\LoginController;
use Tomazo\TestRouter\Controllers\TestController;
use Tomazo\TestRouter\Controllers\CheckAttrController;
use Tomazo\TestRouter\Controllers\DuplicationController;
use Tomazo\TestRouter\Controllers\RecordController;
use Tomazo\TestRouter\Controllers\NoRouteController;
use Tomazo\Router\Exceptions\RouteDuplicationException;
use Tomazo\Router\Exceptions\NoControllersException;
use Tomazo\Router\Exceptions\NoRoutesException;
use Tomazo\Router\RouteLoader\ControllerRouteLoader;

class RouterUnitTest extends TestCase
{
    private $testControllerRouteLoader;

    public function setUp(): void
    {
        $this->testControllerRouteLoader = new TestControllerRouteLoader('','');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clearing superglobal arrays after each test
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
    }

    public function testGetIndexRoute(): void
    {
         //checking if IndexController has method index
         $this->assertTrue(method_exists(IndexController::class, 'index'), "Method 'index' not exist in IndexController class");
        //create instance reflection of IndexController
        $reflectionMethod = new ReflectionMethod(IndexController::class,'index');
        //get attributes from method
        $attributes  = $reflectionMethod->getAttributes(Route::class);

        //assert whether method has attribute Route
        $this->assertNotEmpty($attributes, "Method 'index' dont have adnotation Route");

        $this->testControllerRouteLoader->registerController(IndexController::class);

        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

        $expectedRoutes = [
            [
                'name' => 'index',
                'method' => ['GET'],
                'route' => '/index',
                'action' => [IndexController::class, 'index']
            ]
        ];

        $routes = $router->getRoutes();
        
        $this->assertEquals($expectedRoutes, $routes);

    }

    public function testLoadRoutesFromMultipleControllers()
    {
        /**
         * IndexController::index
         * LoginController::login
         * LoginController::logout
         * 
         * only this method has adnotation
         */
        $routesCount = 3;

        $this->testControllerRouteLoader->registerController(IndexController::class);
        $this->testControllerRouteLoader->registerController(LoginController::class);
    
        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

        $expectedRoutes = [
            [
                'name' => 'index',
                'method' => ['GET'],
                'route' => '/index',
                'action' => [IndexController::class, 'index']
            ],
            [
                'name' => 'login',
                'method' => ['GET'],
                'route' => '/login',
                'action' => [LoginController::class, 'login']
            ],
            [
                'name' => 'logout',
                'method' => ['GET'],
                'route' => '/logout',
                'action' => [LoginController::class, 'logout']
            ]
        ];

        $routes = $router->getRoutes();
        
        $this->assertEquals($expectedRoutes, $routes);
        
        //chcecking if count routes is equals number of controllers
        $this->assertEquals($routesCount, count($routes), "Number of routes isnt equals like number of methods");

    }

    public function testIgnorePrivateMethod()
    {
            $this->testControllerRouteLoader->registerController(TestController::class);

            $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

            $routes = $router->getRoutes();

            foreach($routes as $route) {
                // Assert that private method was not found.
                $this->assertNotEquals('/protect', $route['route']);
                $this->assertNotEquals('/priv', $route['route']);
            }
    
    }
    
    public function testDiffenretHttpMethod()
    {
        $this->testControllerRouteLoader->registerController(TestController::class);

        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

        $routes = $router->getRoutes();
       
        // Search for the route matching '/t/index' with the GET method.
        $routeFound = false;
        foreach($routes as $route) {
            if($route['route'] === '/t/index' && $route['method'] === ['GET']) {
                $routeFound = true;
                break;
            }
        }

        // Assert that the expected route was found.
        $this->assertTrue($routeFound);

         // Search for the route matching '/test' with the POST method from method testMethod.
         $routeFound = false;
         foreach($routes as $route) {
             if($route['route'] === '/test' && $route['method'] === ['POST']) {
                 $routeFound = true;
                 break;
             }
         }

         // Assert that the expected route was found.
        $this->assertTrue($routeFound);
    }

    public function testDuplicationRouteThrowException(): void
    {

        $this->expectException(RouteDuplicationException::class);
        $this->expectExceptionMessage("Route duplication detected. Route: 'dupl' with path '/a' already exists (method: b).");
        $this->expectExceptionCode(500);

        $this->testControllerRouteLoader->registerController(DuplicationController::class);

        new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

    }

    public function testNoControllersAvailable(): void
    {
        $this->expectException(NoControllersException::class);
        $this->expectExceptionMessage("No controllers detected in direction '' or namespace ''.");
        $this->expectExceptionCode(500);

        new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

    }

    public function testNoRoutesFound(): void
    {
        
        
        $this->expectException(NoRoutesException::class);
        $this->expectExceptionMessage("No routes detected in direction '' or namespace ''.");
        $this->expectExceptionCode(500);

        $this->testControllerRouteLoader->registerController(NoRouteController::class);
        new Router($this->testControllerRouteLoader, new SimpleRouteResolver());
    }

    public function testRouteWithParameters(): void
    {

        $this->testControllerRouteLoader->registerController(RecordController::class);

        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

        $expectedRoutes = [
            [
                'name' => 'show',
                'method' => ['GET'],
                'route' => '/test/show/{id}',
                'action' => [RecordController::class, 'show']
            ]
        ];

        $routes = $router->getRoutes();
        
        $this->assertEquals($expectedRoutes, $routes);
    }

    public function testControllerRouteLoader(): void
    {
        $controllerRouteLoader = new ControllerRouteLoader('Tomazo\TestRouter\ControllersLoad',__DIR__ . '/../ControllersLoad');

        $router = new Router($controllerRouteLoader, new SimpleRouteResolver());

        $routes = $router->getRoutes();

        $this->assertEquals(6, count($routes));
    }
  
}