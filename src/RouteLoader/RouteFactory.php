<?php 

namespace Tomazo\Router\RouteLoader;

use Tomazo\Router\Model\Route;
use InvalidArgumentException;

class RouteFactory
{
    /**
     * Creates a Route object from input data with validation on required fields.
     *
     * Validates the structure and types of data for fields in the `$data` array,
     * ensuring all required route components are present and correctly formatted.
     * Throws an InvalidArgumentException if validation fails for any field.
     *
     * @param array $data Associative array containing route details with the following keys:
     *  - 'name' (string): The name identifier for the route.
     *  - 'method' (array): HTTP methods for the route.
     *  - 'route' (string): The path pattern for the route.
     *  - 'action' (array): Array with exactly two elements specifying the controller and method.
     *
     * @return Route Returns a validated Route object.
     *
     * @throws InvalidArgumentException if any required field is missing or has an invalid type.
     */
    public static function createRoute(array $data): Route
    {
        // Field validation for 'name'
        if (!isset($data['name']) || !is_string($data['name']) || empty($data['name'])) {
            throw new InvalidArgumentException("Invalid 'name': must be a non-empty string.");
        }

        // Field validation for 'method'
        if (!isset($data['method']) || !is_array($data['method']) || empty($data['method'])) {
            throw new InvalidArgumentException("Invalid 'method': must be a non-empty array.");
        }

        // Field validation for 'route'
        if (!isset($data['route']) || !is_string($data['route']) || empty($data['route'])) {
            throw new InvalidArgumentException("Invalid 'route': must be a non-empty string.");
        }

        // Field validation for 'action'
        if (!isset($data['action']) || !is_array($data['action']) || count($data['action']) !== 2) {
            throw new InvalidArgumentException("Invalid 'action': must be an array with exactly two elements.");
        }

        // Tworzymy instancję Route
        return new Route(
            $data['name'],
            $data['method'],
            $data['route'],
            $data['action']
        );
    }
}