<?php 

namespace Tomazo\Router\Helpers;

use ReflectionMethod;

class RouteMatcher
{
    public function __construct(private string $path, private ReflectionMethod $method, private string $routePattern)
    {
        
    }

    public function match(): bool
    {
        //Create a regular expression based on a route pattern
        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', str_replace('/', '\/', $this->routePattern));
        if (!preg_match('/^' . $pattern . '$/', $this->path, $matches)) {
            return false;
        }
        //Obtaining method parameters using reflection
        $parameters = $this->method->getParameters();

        //Checking each method parameter against matched path segments
        foreach($parameters as $index => $parameter) {
            $paramType = (string) $parameter->getType();
            $value = $matches[$index + 1] ?? null;

            //Parameter type verification
            if($paramType === 'int' && !filter_var($value, FILTER_VALIDATE_INT)) {
                return false; //Mismatch for int parameter
            } elseif ($paramType === 'string' && !is_string($value)) {
                return false; //Mismatch for string parameter
            }

            return true;
        }
    }
}