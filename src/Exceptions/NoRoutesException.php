<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class NoRoutesException extends Exception
{
    public function __construct(string $direction,int $code = 500)
    {
        $message = "No routes detected in direction '$direction'.";
        parent::__construct($message, $code);
    }
}