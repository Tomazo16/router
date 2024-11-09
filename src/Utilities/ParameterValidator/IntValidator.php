<?php 

namespace Tomazo\Router\Utilities\ParameterValidator;

class IntValidator extends BaseValidator
{
    public function validate(string $type, $value): bool
    {
        if($type === 'int') {
            return filter_var($value, FILTER_VALIDATE_INT) !== false;
        }
        return parent::validate($type,$value);
    }

    public function prepare($value)
    {
        return (int) $value;
    }
}