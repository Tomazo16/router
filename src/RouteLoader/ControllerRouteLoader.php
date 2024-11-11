<?php 

namespace Tomazo\Router\RouteLoader;

use ReflectionMethod;
use Tomazo\Router\RouteLoader\RouteLoaderInterface;
use Tomazo\Router\Attribute\Route;
use Tomazo\Router\Exceptions\NoControllersException;
use Tomazo\Router\RouteLoader\RouteFactory;
use Tomazo\Router\Helpers\RouteValidator;


class ControllerRouteLoader implements RouteLoaderInterface
{
    // Holds an array of routes generated for all loaded controllers
    protected array $routes = [];

    /**
    * Constructor method to initialize the namespace and path to controller files.
    *
    * @param string $namespace       The namespace where the controllers are located
    * @param string $controllersPath The path to the directory containing controller files
    */
    public function __construct(protected string $namespace, protected string $controllersPath)
    {

    }

    /**
     * Loads routes by scanning the directory for controller files and registering routes.
     *
     * @return array Returns an array of all registered routes
     * @throws NoControllersException If no controllers are found or no routes are registered
     */
    public function loadRoute(): array
    {
        // Normalize the directory path to use consistent directory separators
        $directoryPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->controllersPath);
        $diretoryPath = rtrim($directoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Find all files ending in 'Controller.php' in the specified directory
        $files = glob($diretoryPath . '*Controller.php');
    
        foreach($files as $file) {
            // Create the fully qualified class name by combining namespace and file name
            $className = $this->namespace . '\\' . basename(str_replace('.php' , '', $file));

             // Check if the class exists and if so, register its routes
            if(class_exists($className)) {
                $this->registerRoutesForController(new $className);
            }
        }

         // If no routes were registered, throw an exception
        if(empty($this->routes)) {
            throw new NoControllersException($this->namespace, $this->controllersPath);
        }
        return $this->routes;
    }

     /**
     * Registers routes for a given controller by inspecting its public methods for route attributes.
     *
     * @param object $controller The controller instance for which to register routes
     */
    protected function registerRoutesForController($controller): void
    {
       
        $reflection = new \ReflectionClass($controller);
        
        // Iterate over each public method in the controller class
        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Get any attributes of type Route associated with the method
            $attributes = $method->getAttributes(Route::class);

            // Only process methods that belong to this class and have the Route attribute
            if($method->class === $reflection->getName() && !empty($attributes)) {
                $attributes = $attributes[0];

                // Create a new instance of the Route attribute
                $routeInstance = $attributes->newInstance();

                 // Validate the uniqueness of the route to avoid duplicates
                RouteValidator::checkIsRouteExist($this->routes, $routeInstance->getName(), $routeInstance->getPath(), $method->getName());
                
                 // Add the validated route to the routes array using RouteFactory
                $this->routes[] = RouteFactory::createRoute([
                    'name' => $routeInstance->getName(),
                    'method' => $routeInstance->getMethods(), 
                    'route' => $routeInstance->getPath(),
                    'action' =>[$controller, $method->getName()]
                ]);
            }
        }
        
        // Final validation to check if there are any registered routes
        RouteValidator::checkNoRoutes($this->routes, $this->namespace, $this->controllersPath);
 
    }
}