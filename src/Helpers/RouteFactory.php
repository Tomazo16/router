<?php 

namespace Tomazo\Router\Helpers;

class RouteFactory
{
    /**
     * Creates a route array from input data with validation on required fields.
     *
     * This method validates the structure and types of data for fields in the `$data` array,
     * ensuring all required route components are present and correctly formatted. 
     * Throws an InvalidArgumentException if validation fails for any field.
     *
     * @param array $data Associative array containing route details with the following keys:
     *  - 'name' (string): The name identifier for the route. Must be a non-empty string.
     *  - 'method' (array): HTTP methods for the route. Must be a non-empty array.
     *  - 'route' (string): The path pattern for the route. Must be a string.
     *  - 'action' (array): Array with exactly two elements specifying the controller and method 
     *                      (e.g., ['ControllerClass', 'methodName']). Must be an array of size 2.
     *
     * @return array Returns a validated route array with fields: 'name', 'method', 'route', and 'action'.
     *
     * @throws InvalidArgumentException if any required field is missing or has an invalid type.
     */
    public static function createRoute(array $data): array
    {
        // Field validation 'name'
        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new \InvalidArgumentException("Invalid 'name': must be a non-empty string.");
        }

        // Field validation 'method'
        if (!isset($data['method']) || !is_array($data['method']) || empty($data['method'])) {
            throw new \InvalidArgumentException("Invalid 'method': must be a non-empty array.");
        }

        // Field validation 'route'
        if (!isset($data['route']) || !is_string($data['route'])) {
            throw new \InvalidArgumentException("Invalid 'route': must be a string.");
        }

        // Field validation 'action'
        if (!isset($data['action']) || !is_array($data['action']) || count($data['action']) !== 2) {
            throw new \InvalidArgumentException("Invalid 'action': must be an array with exactly two elements.");
        }

        return [
            'name' => $data['name'],
            'method' => $data['method'],
            'route' => $data['route'],
            'action' => $data['action']
        ];
    }
}