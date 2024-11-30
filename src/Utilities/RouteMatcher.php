<?php 

namespace Tomazo\Router\Utilities;

use ReflectionMethod;
use Tomazo\Router\Utilities\ParameterValidator\ParameterValidator;

class RouteMatcher
{
    private ParameterValidator $parameterValidator;

    public function __construct(private string $path, private ReflectionMethod $method, private string $routePattern)
    {
        $this->parameterValidator = new ParameterValidator();
    }

    public function match(): bool
    {
        //Create a regular expression based on a route pattern
        $parsedPharam = PatternParser::parse($this->path, $this->routePattern);
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
            if(!$this->parameterValidator->isValid($paramType, $value)) {
                throw new \InvalidArgumentException("Value '". $value ."' is not a ". $paramType, 404);
            }
        }
        return true;
    }

    public function execute(): mixed
    {
        $parameters = PatternParser::parse($this->path , $this-> routePattern);

        if($parameters === NULL){
            return false;
        }

        $methodParameters = [];
        
       // Prepare the parameters for the method invocation
       $methodParameters = $this->parameterValidator->prepareParameters($this->method, $parameters);
        
       // Invoke the method with prepared parameters
        return $this->method->invokeArgs($this->method->getDeclaringClass()->newInstance(), $methodParameters);
    }

}