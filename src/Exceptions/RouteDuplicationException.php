<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class RouteDuplicationException extends Exception
{
    public function __construct(string $name,string $path, string $method, int $code = 500)
    {
        $message = "Route duplication detected. Route: '$name' with path '$path' already exists (method: $method).";
        parent::__construct($message, $code);
    }
}