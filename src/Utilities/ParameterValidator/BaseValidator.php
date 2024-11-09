<?php 

namespace Tomazo\Router\Utilities\ParameterValidator;

abstract class BaseValidator implements Validator
{
    private ?Validator $nextValidator = null;

    public function setNext(Validator $validator): Validator
    {
        $this->nextValidator = $validator;
        return $validator;
    }

    public function validate(string $type, $value): bool
    {
        if($this->nextValidator) {
            return $this->nextValidator->validate($type, $value);
        }
        return false;
    }

    public function prepare($value)
    {
        return $value;
    }
}