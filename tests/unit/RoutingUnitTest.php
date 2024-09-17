<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\Router\Router;
use Tomazo\Router\TestControllers\IndexController;
use Tomazo\Router\TestControllers\LoginController;
use Tomazo\Router\Exceptions\RouteDuplicationException;
use Tomazo\Router\Exceptions\NoControllersException;
use Tomazo\Router\Exceptions\NoRoutesException;


class RoutingUnitTest extends TestCase
{
    public function setUp(): void
    {
        if(!class_exists('TestController')){
            eval("

                class TestController
                {   
                    [#Route('GET', '/t/index')]
                    public function index() {}

                    [#Route('POST', '/test')]
                    public function testMethod() {}

                    [#Route('GET', '/protect')]
                    protected function protect() {}

                    [#Route('GET', '/priv')]
                    private function priv() {}
                }
            ");
        }
    }

    public function testGetRoutes(): void
    {
         //checking if IndexController has method index
         $this->assertTrue(method_exists(IndexController::class, 'index'), "Method 'index' not exist in IndexController class");
        //create instance reflection of IndexController
        $reflectionMethod = new ReflectionMethod(IndexController::class,'index');
        //get attributes from method
        $attributes  = $reflectionMethod->getAttributes(Route::class);

        //assert whether method has attribube Route
        $this->assertNotEmpty($attributes, "Method 'index' dont have adnotation Route");

       
        //checking if LognController has method login
        $this->assertTrue(method_exists(LoginController::class, 'login'), "Method 'login' not exist in LoginController class");
        //create instance reflection of LoginController
        $reflectionMethod = new ReflectionMethod(LoginController::class,'login');
        //get attributes from method
        $attributes  = $reflectionMethod->getAttributes(Route::class);

        //assert whether method has attribube Route
        $this->assertNotEmpty($attributes, "Method 'login' dont have adnotation Route");

        
        $router = new Router('test');

        $expectedRoutes = [
            [
                'method' => 'GET',
                'route' => '/index',
                'action' => [new IndexController(), 'index']
            ],
            [
                'method' => 'GET',
                'route' => '/login',
                'action' => [new LoginController(), 'login']
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
         * 
         * only this method has adnotation
         */
        $controllersCount = 2;
        

        $router = new Router('test');
        $routes = $router->getRoutes();

        //chcecking if count routes is equals number of controllers
        $this->assertEquals($controllersCount, count($routes), "Number of routes isnt equals like number of Controllers!");

    }

    public function testIgnorePrivateMethod()
    {
  
            $this->assertTrue(class_exists('TestController'));

            $testController = new TestController();
            $router = new Router('test');

            $reflection = new \ReflectionClass($router);
            $registerRoutesForController = $reflection->getMethod('registerRoutesForController');
            $registerRoutesForController->setAccessible(true);
        
            $registerRoutesForController->invoke($router, $testController);

            $routes = $router->getRoutes();

            foreach($routes as $route) {
                // Assert that private method was not found.
                $this->assertNotEquals('/protect', $route['route']);
                $this->assertNotEquals('/priv', $route['route']);
            }
    
    }
    
    public function testPostHttpMethod()
    {
        $this->assertTrue(class_exists('TestController'));

        $testController = new \TestController();

        $router = new Router('test');

        // Use reflection to access the protected `registerRoutesForController` method in the Router class.
        $reflection = new \ReflectionClass($router);
        $registerRoutesMethod = $reflection->getMethod('registerRoutesForController');
        $registerRoutesMethod->setAccessible(true);

        // Register routes for the mocked controller
        $registerRoutesMethod->invoke($router, $testController);

        $routes = $router->getRoutes();
       
        // Search for the route matching '/t/index' with the GET method.
        $routeFound = false;
        foreach($routes as $route) {
            if($route['route'] === '/t/index' && $route['method'] === 'GET') {
                $routeFound = true;
                break;
            }
        }

        // Assert that the expected route was found.
        $this->assertTrue($routeFound);

         // Search for the route matching '/test' with the POST method from method testMethod.
         $routeFound = false;
         foreach($routes as $route) {
             if($route['route'] === '/test' && $route['method'] === 'POST' && $route['action'][1] === 'testMethod') {
                 $routeFound = true;
                 break;
             }
         }

         // Assert that the expected route was found.
        $this->assertTrue($routeFound);
    }

    public function testDuplicationRouteThrowException(): void
    {
        eval(
            "
            class DuplicationController
            {   
                [#Route('GET','\a')]
                public function a() {}

                [#Route('GET','\a')]
                public function b() {}
            {
            "
        );

        $duplicationController = new DuplicationController();

        $router = new Router('test');

        $reflectionMethod = new \ReflectionMethod(Route::class, 'registerRoutesForController');
        $reflectionMethod->setAccessible(true);

        $this->expectException(RouteDuplicationException::class);
        $this->expectExceptionMessage("Route duplication detected for route '\a' with method 'GET'.");
        $this->expectExceptionCode(404);


        $reflectionMethod->invoke($router, $duplicationController);

    }

    public function testNoControllersAvailable(): void
    {
    
        $this->expectException(NoControllersException::class);
        $this->expectExceptionMessage("No controllers detected in direction ''.");
        $this->expectExceptionCode(404);

        $router = new Router('');
    }

    public function testNoRoutesFound(): void
    {
        $router = new Router('test');
        $reflectionProperty = new \ReflectionProperty(Router::class,'router');
        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue($router, []);
        
        $this->expectException(NoRoutesException::class);
        $this->expectExceptionMessage("No routes detected in direction ''.");
        $this->expectExceptionCode(404);

        $router->getRoutes();
    }
}