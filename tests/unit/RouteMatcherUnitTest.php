<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\TestRouter\Controllers\CheckAttrController;
use Tomazo\Router\Utilities\RouteMatcher;
use Tomazo\Router\Exceptions\TooFewArgToFunctionException;

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

       $this->expectException(InvalidArgumentException::class);
       $this->expectExceptionMessage("Value 'stringInsteadOfInt' is not a int");
       $this->expectExceptionCode(404);

       $routeMatcher->match();
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

    public function testFunctionExecute(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/Tom/details/11';
        $routePattern = '/test/show/{name}/details/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);
        $exec = $routeMatcher->execute();

        $this->assertInstanceOf(CheckAttrController::class, $exec);
    }

    public function testToofewArgToFunctionException(): void
    {
        $this->expectException(TooFewArgToFunctionException::class);
        $this->expectExceptionMessage("Too few arguments to route 'profile', 1 passed and exactly 2 expected");
        $this->expectExceptionCode(404);

        $method = new \ReflectionMethod(CheckAttrController::class, 'profile');
        $path = '/test/show/123/ert';
        $routePattern = '/test/show/{id}/{param}';

        $routeMatcher = new RouteMatcher($path, $method, $routePattern);
        $routeMatcher->execute();
    }
}