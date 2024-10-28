<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\TestRouter\Controllers\CheckAttrController;
use Tomazo\Router\Helpers\RouteMatcher;

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

    public function testRouteDoesNotMatchWithIncorrectType()
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'profile');
        $path = '/test/show/stringInsteadOfInt/45';
        $routePattern = '/test/show/{id}/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertFalse($routeMatcher->match(), 'Expected route not to match due to incorrect type.');
    }

    public function testRouteDoesNotMatchWithMissingSegments()
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'profile');
        $path = '/test/show/12';
        $routePattern = '/test/show/{id}/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertFalse($routeMatcher->match(), 'Expected route not to match due to missing segment.');
    }

    public function testRouteMatchesWithAdditionalDynamicRoutes()
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/Tom/details/11';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertTrue($routeMatcher->match(), 'Expected route to match with additional dynamic route segments.');
    }

    public function testRouteDoesNotMatchWithDifferentPath()
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/123';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);

        $this->assertFalse($routeMatcher->match(), 'Expected route not to match with extra path segment.');
    }
}