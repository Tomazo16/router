<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class NoControllersException extends Exception
{
    public function __construct(string $direction,int $code = 500)
    {
        $message = "No controllers detected in direction '$direction'.";
        parent::__construct($message, $code);
    }
}