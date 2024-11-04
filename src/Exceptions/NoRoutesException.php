<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class NoRoutesException extends Exception
{
    public function __construct(string $namespace, string $direction, int $code = 500)
    {
        $message = "No routes detected in direction '$direction' or namespace '$namespace'.";
        parent::__construct($message, $code);
    }
}