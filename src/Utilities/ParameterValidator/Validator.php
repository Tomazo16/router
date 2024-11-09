<?php 

namespace Tomazo\Router\Utilities\ParameterValidator;

interface Validator 
{
    public function setNext(Validator $validator): Validator;
    public function validate(string $type, $value): bool;
    public function prepare($value);
}