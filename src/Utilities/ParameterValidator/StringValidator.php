<?php 

namespace Tomazo\Router\Utilities\ParameterValidator;

class StringValidator extends BaseValidator
{
    public function validate(string $type, $value): bool
    {
        if($type === 'string') {
            return is_string($value);
        }
        return parent::validate($type, $value);
    }

    public function prepare($value)
    {
        return (string) $value;
    }
}