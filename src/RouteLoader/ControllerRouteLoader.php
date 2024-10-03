<?php 

namespace Tomazo\Router\RouteLoader;

use ReflectionMethod;
use Tomazo\Router\RouteLoader\RouteLoaderInterface;
use Tomazo\Router\Attribute\Route;
use Tomazo\Router\Exceptions\NoRoutesException;
use Tomazo\Router\Exceptions\RouteDuplicationException;
use Tomazo\Router\Helpers\RouteValidator;

class ControllerRouteLoader implements RouteLoaderInterface
{
    protected string $controllersPath ='';
    protected array $routes = [];

    public function __construct(string $controllersPath)
    {
        $this->controllersPath = $controllersPath;
    }

    public function loadRoute(): array
    {
        return $this->routes;
    }

    protected function registerRoutesForController($controller): void
    {
        $reflection = new \ReflectionClass($controller);
        
            foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);
                if($method->class === $reflection->getName() && !empty($attributes)) {
                    $attributes = $attributes[0];
                    $routeInstance = $attributes->newInstance();
                    RouteValidator::checkIsRouteExist($this->routes, $routeInstance->getName(), $routeInstance->getPath(), $method->getName());
                    $this->routes[] = $this->createRouteFromAttributes($method, $routeInstance, $controller);
                }
            }
            
            RouteValidator::checkNoRoutes($this->routes, $this->controllersPath);
 
    }

    protected function createRouteFromAttributes(ReflectionMethod $method, Route $routeInstance, $controller): array
    {
        return [
                'name' => $routeInstance->getName(),
                'method' => $routeInstance->getMethods(),
                'route' => $routeInstance->getPath(),
                'action' => [$controller, $method->getName()]
        ];
        
    }
}