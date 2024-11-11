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
    private $router;

    public function setUp(): void
    {
        $this->routeLoaderMock = $this->createMock(RouteLoaderInterface::class);
        $this->routeResolverMock = $this->createMock(RouteResolverInterface::class);
       
    }

    public function testGetRoutesReturnsResolvedRoutes(): void
    {
        $routesData[] = new Route('show', ['GET'], '/test/show/{id}', ['Controller', 'show']);

        $this->routeLoaderMock->expects($this->once())
            ->method('loadRoute')
            ->willReturn($routesData);

        $this->routeResolverMock->expects($this->exactly(1))
            ->method('resolveRoute')
            ->with($routesData[0])
            ->willReturn(['Resolved Route']);
        
            $this->router = new Router($this->routeLoaderMock, $this->routeResolverMock);
        $expectedRoutes = [['Resolved Route']];

        $this->assertEquals($expectedRoutes, $this->router->getRoutes());
    }
}