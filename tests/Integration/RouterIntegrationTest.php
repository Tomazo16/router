<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\Router\Model\Route;
use Tomazo\Router\Router;
use Tomazo\Router\RouteLoader\RouteLoaderInterface;
use Tomazo\Router\RouteResolver\RouteResolverInterface;

class RouterIntegrationTest extends TestCase
{
    private $routeLoaderMock;
    private $routeResolverMock;
    private array $routesData;

    public function setUp(): void
    {
        $this->routeLoaderMock = $this->createMock(RouteLoaderInterface::class);
        $this->routeResolverMock = $this->createMock(RouteResolverInterface::class);
        $this->routesData[] = new Route('show', ['GET'], '/test/show/{id}', ['Controller', 'show']);
    }

    public function testGetRoutesReturnsResolvedRoutes(): void
    {


        $this->routeLoaderMock->expects($this->once())
            ->method('loadRoute')
            ->willReturn($this->routesData);

        $this->routeResolverMock->expects($this->exactly(1))
            ->method('resolveRoute')
            ->with($this->routesData[0])
            ->willReturn(['Resolved Route']);
        
        $router = new Router($this->routeLoaderMock, $this->routeResolverMock);    
        $expectedRoutes = [['Resolved Route']];

        $this->assertEquals($expectedRoutes, $router->getRoutes());
    }

    public function testGetRoutePathsReturnsRoutePaths()
    {

        $this->routeLoaderMock->expects($this->once())
            ->method('loadRoute')
            ->willReturn($this->routesData);

        $this->routeResolverMock->expects($this->exactly(1))
            ->method('getRoutePaths')
            ->with($this->routesData[0])
            ->willReturn('GET: /test/show/{id} | name: show');


        $router = new Router($this->routeLoaderMock, $this->routeResolverMock);    
        $expectedPaths = ['GET: /test/show/{id} | name: show'];

        $this->assertEquals($expectedPaths, $router->getRoutePaths());
    }

    public function testEmptyRoutesList()
    {
        // A mock configuration for the route loader that will return an empty list of routes
        $this->routeLoaderMock->expects($this->once())
            ->method('loadRoute')
            ->willReturn([]);

        $router = new Router($this->routeLoaderMock, $this->routeResolverMock); 

        // Checking that getRoutes and getRoutePaths return empty lists
        $this->assertEquals([], $router->getRoutes());
        $this->assertEquals([], $router->getRoutePaths());
    }

    public function testGenerateUrlWithValidParameters(): void
    {
        $this->routeLoaderMock->expects($this->once())
            ->method('loadRoute')
            ->willReturn($this->routesData);

        $router = new Router($this->routeLoaderMock, $this->routeResolverMock);
        $url = $router->generateUrl('show', ['id' => 1]);

        $this->assertEquals('/test/show/1', $url);
    }

}