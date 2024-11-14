<?php 

namespace Tomazo\Router\Exceptions;

use Exception;

class TooFewArgToFunctionException extends Exception
{
    public function __construct(string $routeName, int $paramPassed, int $paramExpected, int $code = 404)
    {
        $message = "Too few arguments to route '{$routeName}', {$paramPassed} passed and exactly {$paramExpected} expected";
        parent::__construct($message, $code);
    }
}