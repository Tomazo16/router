<?php 

namespace Tomazo\Router\Tests;

use PHPUnit\Framework\TestCase;
use Tomazo\Router\Utilities\RouteUrlGenerator;
use Tomazo\Router\Model\Route;
use Tomazo\Router\Exceptions\RouteNotFoundException;

class RouteUrlGeneratorUnitTest extends TestCase
{
    private RouteUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $routes = [
            new Route('show', ['GET'], '/test/show/{id}', ['Controller', 'show']),
            new Route('profile', ['GET'], '/user/{username}', ['UserController', 'profile']),
            new Route('details', ['GET'], '/item/{category}/{id}', ['ItemController', 'details'])
        ];
        $this->urlGenerator = new RouteUrlGenerator($routes);
    }

    public function testGenerateUrlWithValidParameters(): void
    {
        $url = $this->urlGenerator->generateUrl('show', ['id' => 123]);
        $this->assertEquals('/test/show/123', $url);

        $url = $this->urlGenerator->generateUrl('profile', ['username' => 'john_doe']);
        $this->assertEquals('/user/john_doe', $url);
        
        $url = $this->urlGenerator->generateUrl('details', ['category' => 'books', 'id' => 45]);
        $this->assertEquals('/item/books/45', $url);
    }

    public function testGenerateUrlWithMissingParameter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing parameter: id");

        $this->urlGenerator->generateUrl('show'); // 'id' parameter is missing
    }

    public function testGenerateUrlWithExtraParameter(): void
    {
        $url = $this->urlGenerator->generateUrl('show', ['id' => 123, 'extra' => 'unused']);
        $this->assertEquals('/test/show/123', $url); // Extra parameters are ignored
    }

    public function testGenerateUrlWithNonExistentRoute(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->expectExceptionMessage("Route with name 'non_existent' not found.");
        $this->expectExceptionCode(500);

        $this->urlGenerator->generateUrl('non_existent', ['id' => 123]);
    }

    public function testGenerateUrlWithMultipleParameters(): void
    {
        $url = $this->urlGenerator->generateUrl('details', ['category' => 'books', 'id' => 123]);
        $this->assertEquals('/item/books/123', $url);
    }

    public function testGenerateUrlWithIncorrectParameterTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing parameter: id");

        $this->urlGenerator->generateUrl('show', ['id' => null]); // 'id' should not be null
    }
}
