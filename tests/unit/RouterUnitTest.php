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

    /**
     * Test to verify if the IndexController has an 'index' route with a Route attribute.
     */
    public function testGetIndexRoute(): void
    {
         $this->assertTrue(method_exists(IndexController::class, 'index'), "Method 'index' not exist in IndexController class");

        $reflectionMethod = new ReflectionMethod(IndexController::class,'index');
        $attributes  = $reflectionMethod->getAttributes(Route::class);

        $this->assertNotEmpty($attributes, "Method 'index' does not have Route annotation");

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

    /**
     * Test to load routes from multiple controllers and verify expected route count and details.
     */
    public function testLoadRoutesFromMultipleControllers()
    {
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
        $this->assertEquals($routesCount, count($routes), "Number of routes does not match expected count");
    }

    /**
     * Test to check if private methods are ignored in route registration.
     */
    public function testIgnorePrivateMethod()
    {
        $this->testControllerRouteLoader->registerController(TestController::class);
        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

        $routes = $router->getRoutes();

        foreach ($routes as $route) {
            $this->assertNotEquals('/protect', $route['route']);
            $this->assertNotEquals('/priv', $route['route']);
        }
    }
    
    /**
     * Test to check that routes with different HTTP methods are correctly registered.
     */
    public function testDiffenretHttpMethod()
    {
        $this->testControllerRouteLoader->registerController(TestController::class);
        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver());

        $routes = $router->getRoutes();
       
        $routeFound = false;
        foreach ($routes as $route) {
            if ($route['route'] === '/t/index' && $route['method'] === ['GET']) {
                $routeFound = true;
                break;
            }
        }
        $this->assertTrue($routeFound);

        $routeFound = false;
        foreach ($routes as $route) {
            if ($route['route'] === '/test' && $route['method'] === ['POST']) {
                $routeFound = true;
                break;
            }
        }
        $this->assertTrue($routeFound);
    }

    /**
     * Test to check route with URL parameters is correctly registered.
     */
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

    public function testgetRoutePaths(): void
    {
        $this->testControllerRouteLoader->registerController(CheckAttrController::class);

        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver);

        $expectedRoutePaths = [
            'GET : /test/show/{id} | name : show',
            'GET : /test/show/{name}/details/{param} | name : showDetails',
            'GET : /test/show/{id}/{param} | name : profile'
        ];

        $routePaths = $router->getRoutePaths();

        $this->assertEquals($expectedRoutePaths, $routePaths);
    }

    public function testGetActionMethod(): void
    {
        $path = '/test/show/123';

        $this->testControllerRouteLoader->registerController(CheckAttrController::class);

        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver);

        $this->assertEquals('hello world', $router->getActionMethod($path));
    }

    public function testGetActionMethodException(): void
    {
        $path = '/wrong/path/123';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No route matches the provided path: /wrong/path/123");
        $this->expectExceptionCode(404);

        $this->testControllerRouteLoader->registerController(CheckAttrController::class);
        $router = new Router($this->testControllerRouteLoader, new SimpleRouteResolver);
        $router->getActionMethod($path);
    }
}