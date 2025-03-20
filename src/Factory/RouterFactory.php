<?php

namespace Tomazo\Router\Factory;

use Exception;
use InvalidArgumentException;
use ReflectionClass;
use Tomazo\Router\Router;

/**
 * Class RouterFactory
 *
 * Factory responsible for creating a Router instance based on a configuration array.
 * It validates the required classes, checks constructor arguments using reflection,
 * and dynamically creates the necessary instances.
 */
class RouterFactory
{
    /**
     * Creates a Router instance based on the provided configuration.
     *
     * @param array $config Configuration array containing 'routeLoader' and 'routeResolver' definitions.
     * @return Router|null
     * @throws Exception If required keys or classes are missing or incorrect.
     * @throws InvalidArgumentException If required constructor parameters are missing.
     */
    public static function createRouter(array $config): ?Router
    {
        // Validate the presence of required configuration keys
        if (!key_exists('routeLoader', $config) || !key_exists('routeResolver', $config)) {
            throw new Exception("There are no 'routeLoader' or 'routeResolver' keys in the configuration");
        }

        // Validate the presence of 'class' keys inside both routeLoader and routeResolver
        if (!key_exists('class', $config['routeLoader']) || !key_exists('class', $config['routeResolver'])) {
            throw new Exception("There is no 'class' key in the routeLoader or routeResolver array");
        }

        $routeLoader = $config['routeLoader']['class'];
        $routeResolver = $config['routeResolver']['class'];

        // Ensure both classes exist
        if (!class_exists($routeLoader)) {
            throw new Exception("Route Loader Class {$routeLoader} does not exist");
        }
        if (!class_exists($routeResolver)) {
            throw new Exception("Route Resolver Class {$routeResolver} does not exist");
        }

        // Validate 'attr' array presence for the routeLoader
        if (!isset($config['routeLoader']['attr']) || !is_array($config['routeLoader']['attr'])) {
            throw new Exception("There is no 'attr' array in the routeLoader configuration");
        }

        // Check if the provided attributes match the constructor requirements
        self::checkInstanceArgs($config['routeLoader']);
        self::checkInstanceArgs($config['routeResolver']);

        // Dynamically create instances of the routeLoader and routeResolver
        $routeLoaderInstance = self::createInstance($routeLoader, $config['routeLoader']['attr']);
        $routeResolverInstance = self::createInstance($routeResolver, $config['routeResolver']['attr']);

        // Return a new Router instance with the created dependencies
        return new Router($routeLoaderInstance, $routeResolverInstance);
    }

    /**
     * Validates if the provided attributes match the constructor parameters of the class.
     *
     * @param array $row Configuration for the specific class.
     * @return array
     * @throws InvalidArgumentException If a required constructor parameter is missing in 'attr'.
     */
    private static function checkInstanceArgs(array $row): array
    {
        $className = $row['class'];

        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        // If there is no constructor, no need to check parameters
        if (!$constructor) {
            return $row;
        }

        $params = $constructor->getParameters();
        $attr = $row['attr'];

        // Check if all required constructor parameters are present in 'attr'
        foreach ($params as $param) {
            $name = $param->getName();

            if (!array_key_exists($name, $attr)) {
                throw new InvalidArgumentException("Missing required parameter '$name' for $className");
            }
        }
        return $row;
    }

    /**
     * Dynamically creates an instance of a class with the provided attributes as constructor arguments.
     *
     * @param string $className Fully qualified class name.
     * @param array $attr Attributes to pass as named arguments to the constructor.
     * @return object
     */
    private static function createInstance(string $className, array $attr): object
    {
        return new $className(...$attr);
    }
}
