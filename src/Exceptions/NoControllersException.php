<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class NoControllersException extends Exception
{
    public function __construct(string $namespace, string $direction,int $code = 500)
    {
        $message = "No controllers detected in direction '$direction' or namespace '$namespace'.";
        parent::__construct($message, $code);
    }
}