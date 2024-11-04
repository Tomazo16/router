<?php 

namespace Tomazo\Router\Utilities;

use ReflectionMethod;

class RouteMatcher
{
    public function __construct(private string $path, private ReflectionMethod $method, private string $routePattern)
    {
        
    }

    public function match(): bool
    {
        //Create a regular expression based on a route pattern
        $parsedPharam = $this->parse();
        if($parsedPharam === null){
            return false; //The path does not match the pattern
        }

        //Obtaining method parameters using reflection
        $parameters = $this->method->getParameters();

        //Checking each method parameter against matched path segments
        foreach($parameters as $index => $parameter) {
            $paramName = $parameter->getName();
            $paramType = (string) $parameter->getType();
            $value = $parsedPharam[$paramName] ?? null;

            //Parameter type verification
            if($paramType === 'int' && !filter_var($value, FILTER_VALIDATE_INT)) {
                return false; //Mismatch for int parameter
            } elseif ($paramType === 'string' && !is_string($value)) {
                return false; //Mismatch for string parameter
            }

            return true;
        }
    }

    public function execute(): mixed
    {
        $parameters = $this->parse();

        if($parameters === NULL){
            return false;
        }

        $methodParameters = [];

        foreach($this->method->getParameters() as $param) {
            $paramName = $param->getName();
            $paramType = (string) $param->getType();

            if(isset($parameters[$paramName])) {
                $value = $parameters[$paramName];

                if($paramType === 'int') {
                    $methodParameters[] = (int) $value;
                }   elseif( $paramType === 'string') {
                    $methodParameters[] = (string) $value;
                }
            }
        }
        
        return $this->method->invokeArgs($this->method->getDeclaringClass()->newInstance(), $methodParameters);
    }

    public function parse(): array
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', str_replace('/', '\/', $this->routePattern));
        if (!preg_match('/^' . $pattern . '$/', $this->path, $matches)) {
            return [];
        }

        // Return matched parameters as an associative array
        preg_match_all('/\{(\w+)\}/', $this->routePattern, $paramNames);
        array_shift($matches);
        return array_combine($paramNames[1], $matches);
    }
}