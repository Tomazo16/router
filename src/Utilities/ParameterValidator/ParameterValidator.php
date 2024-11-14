<?php 

namespace Tomazo\Router\Utilities\ParameterValidator;

use ReflectionMethod;
use Tomazo\Router\Exceptions\TooFewArgToFunctionException;

class ParameterValidator
{
    private $chain;

    public function __construct()
    {
        $this->chain = new IntValidator();
        $this->chain->setNext(new StringValidator());
    }

    public function isValid(string $type, $value): bool
    {
        return $this->chain->validate($type, $value);
    }

    public function prepareParameters(ReflectionMethod $method, array $parameters): array
    {
        $methodParameters = [];

        foreach($method->getParameters() as $param) {
            $paramName = $param->getName();
            $paramType = (string) $param->getType();

            if(isset($parameters[$paramName]) && $this->isValid($paramType, $parameters[$paramName])) {
                $methodParameters[] = $this->chain->prepare($parameters[$paramName]);
            }
        }

        $countParameters = count($methodParameters);
        if($countParameters != $method->getNumberOfRequiredParameters()) {
            throw new TooFewArgToFunctionException($method->getName(), $countParameters, $method->getNumberOfRequiredParameters());
        }
        return $methodParameters;
    }
}