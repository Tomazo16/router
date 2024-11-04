<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\TestRouter\Controllers\CheckAttrController;
use Tomazo\Router\Utilities\RouteMatcher;

class RouteMatcherUnitTest extends TestCase
{
    public function testRouteMatchesWithCorrectType(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'show');
        $path = '/test/show/123';
        $routePattern = '/test/show/{id}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertTrue($routeMatcher->match(), 'Expected route to match with correct types.');
    }

    public function testRouteDoesNotMatchWithIncorrectType(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'profile');
        $path = '/test/show/stringInsteadOfInt/45';
        $routePattern = '/test/show/{id}/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertFalse($routeMatcher->match(), 'Expected route not to match due to incorrect type.');
    }

    public function testRouteDoesNotMatchWithMissingSegments(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'profile');
        $path = '/test/show/12';
        $routePattern = '/test/show/{id}/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertFalse($routeMatcher->match(), 'Expected route not to match due to missing segment.');
    }

    public function testRouteMatchesWithAdditionalDynamicRoutes(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/Tom/details/11';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertTrue($routeMatcher->match(), 'Expected route to match with additional dynamic route segments.');
    }

    public function testRouteDoesNotMatchWithDifferentPath(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/123';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertFalse($routeMatcher->match(), 'Expected route not to match with extra path segment.');
    }

    public function testRouteParser(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/Tom/details/11';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);
        $params = $routeMatcher->parse();

        //check whether the arguments are valid
        $this->assertEquals(['name' => 'Tom', 'param' => '11'], $params);

    }

    public function testFuncionExecute(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/Tom/details/11';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);
        $exec = $routeMatcher->execute();

        $this->assertInstanceOf(CheckAttrController::class, $exec);
    }
}