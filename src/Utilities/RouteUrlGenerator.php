<?php 

namespace Tomazo\Router\Utilities;

use Tomazo\Router\Exceptions\RouteNotFoundException;
use Tomazo\Router\Model\Route;

class RouteUrlGenerator
{
    public function __construct(private array $routes)
    {
        
    }

     /**
     * Generates a URL for a named route, replacing parameters in the route pattern.
     *
     * @param string $routeName The name of the route to generate a URL for.
     * @param array $parameters Associative array of parameters to replace in the route pattern.
     * @return string Generated URL with parameters replaced.
     * @throws RouteNotFoundException if the route name does not exist.
     * @throws \InvalidArgumentException if required parameters are missing.
     */
    public function generateUrl(string $routeName, array $parameters = []): string
    {
         // Find the route by name
         $route = $this->findRouteByName($routeName);
        
         // Get the route pattern
         $pattern = $route->getRoute();
 
         // Replace placeholders with provided parameters
         $url = preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($parameters) {
             $paramName = $matches[1];
             if (!isset($parameters[$paramName])) {
                 throw new \InvalidArgumentException("Missing parameter: {$paramName}");
             }
             return $parameters[$paramName];
         }, $pattern);
 
         return '/' .$this->getRoutePrefix() . $url;
    }

    /**
     * Finds a route by its name from the list of routes.
     *
     * @param string $routeName The name of the route.
     * @return Route The matched route.
     * @throws RouteNotFoundException if the route is not found.
     */
    private function findRouteByName(string $routeName): Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $routeName) {
                return $route;
            }
        }

        throw new RouteNotFoundException($routeName);
    }

    /**
     * Returns the first segment of the request URI, including trailing slash.
     *
     * Example:
     * URL: /ttfinance/acc/edit/38 → returns "ttfinance"
     * URL: / → returns null
     *
     * @return string|null
     */
    public function getRoutePrefix(): ?string
    {
        // Get only the path (without query string, e.g., ?foo=bar)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove leading and trailing slashes, then split by "/"
        $segments = explode('/', trim($path, '/'));

        // Return first segment with trailing slash or null if not found
        return isset($segments[0]) && $segments[0] !== '' ? $segments[0] : null;
    }
}