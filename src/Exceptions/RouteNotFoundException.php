<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct(string $routeName, int $code = 500)
    {
        $message = "Route with name '{$routeName}' not found.";
        parent::__construct($message, $code);
    }
}