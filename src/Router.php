<?php 

namespace Tomazo\Router;

use Exception;
use Tomazo\Router\RouteLoader\RouteLoaderInterface;
use Tomazo\Router\RouteResolver\RouteResolverInterface;
use Tomazo\Router\Utilities\RouteUrlGenerator;

/**
 * The Router class is responsible for managing and interacting with routes.
 * It integrates route loading, resolution, and URL generation functionalities.
 */
class Router
{
    /**
     * @var array $routes Stores the loaded routes.
     */
    private $routes = [];

    /**
     * @var RouteUrlGenerator $urlGenerator Handles the generation of URLs for routes.
     */
    private RouteUrlGenerator $urlGenerator;

    /**
     * Constructor initializes the Router with a RouteLoader and a RouteResolver.
     * Routes are loaded during initialization, and a URL generator is created
     * based on the loaded routes.
     *
     * @param RouteLoaderInterface $routeLoader Loads routes from a data source.
     * @param RouteResolverInterface $routeResolver Resolves and processes routes.
     */
    public function __construct(
        private RouteLoaderInterface $routeLoader,
        private RouteResolverInterface $routeResolver
    ) {
        $this->routes = $routeLoader->loadRoute(); // Load all routes.
        $this->urlGenerator = new RouteUrlGenerator($this->routes); // Initialize the URL generator.
    }

    /**
     * Retrieves all resolved routes in an array format.
     *
     * @return array The resolved routes.
     */
    public function getRoutes(): array
    {
        $routes = [];

        // Resolve each route and add it to the routes array.
        foreach ($this->routes as $route) {
            $routes[] = $this->routeResolver->resolveRoute($route);
        }
        return $routes;
    }

    /**
     * Matches the given path to a route and calls the associated action.
     *
     * @param string $path The path to match against routes.
     * @return mixed The result of the route's action if matched.
     *
     * @throws \InvalidArgumentException If no route matches the given path.
     */
    public function getActionMethod(string $path): mixed
    {
        foreach ($this->routes as $route) {
            // Try to match the route and call its action.
            $result = $this->routeResolver->callAction($path, $route);
            
            if ($result !== false) {
                return $result; // Return the action result if matched.
            }
        }

        // Throw an exception if no route matches the provided path.
        throw new \InvalidArgumentException("No route matches the provided path: {$path}", 404);
    }

    /**
     * Retrieves the paths of all routes in a human-readable format.
     *
     * @return array An array of route paths.
     */
    public function getRoutePaths(): array
    {
        $routePaths = [];

        // Generate path strings for each route.
        foreach ($this->routes as $route) {
            $routePaths[] = $this->routeResolver->getRoutePaths($route);
        }

        return $routePaths;
    }

    /**
     * Generates a URL for a specific route, substituting placeholders with parameters.
     *
     * @param string $routeName The name of the route.
     * @param array $parameters Parameters to replace placeholders in the route's URL.
     * @return string The generated URL.
     *
     * @throws \InvalidArgumentException If the route name or parameters are invalid.
     */
    public function generateUrl(string $routeName, array $parameters): string
    {
        return $this->urlGenerator->generateUrl($routeName, $parameters);
    }

    /**
     * Redirects the user to a generated URL based on a named route and its parameters.
     *
     * @param array $route       An associative array with keys:
     *                           - 'name': (string) The route name to generate the URL.
     *                           - 'param': (array) Parameters to be passed to the URL generator.
     * @param int   $statusCode  HTTP status code for the redirect (default is 302 - Found).
     *
     * @throws \RuntimeException If required keys are missing in the route array, key param is not a array,
     *                           or if headers have already been sent.
     *
     * @return never
     */
    public function redirect(array $route, int $statusCode = 302): never
    {
        if (!array_key_exists('name', $route) || !array_key_exists('param', $route)) {
            throw new \RuntimeException("Array 'route' do not have keys 'name' or 'param'");
        }
        if (!is_array($route['param'])) {
            throw new \RuntimeException("Key 'param' is not a array");
        }

        $url = $this->urlGenerator->generateUrl($route['name'], $route['param']);

        if (!headers_sent()){
            header ('Location:' . $url, true, $statusCode);
            exit;
        }
        throw new \RuntimeException("Headers already sent. Cannot redirect to {$route['name']}");
    }
}