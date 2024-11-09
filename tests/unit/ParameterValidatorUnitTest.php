<?php

use PHPUnit\Framework\TestCase;
use Tomazo\Router\Utilities\ParameterValidator\ParameterValidator;
use Tomazo\Router\Utilities\ParameterValidator\IntValidator;
use Tomazo\Router\Utilities\ParameterValidator\StringValidator;

class ParameterValidatorUnitTest extends TestCase
{
    private ParameterValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ParameterValidator();
    }

    public function testValidIntType(): void
    {
        $isValid = $this->validator->isValid('int', 123);
        $this->assertTrue($isValid, "It should return true for a valid int value");
    }

    public function testInvalidIntType(): void
    {
        $isValid = $this->validator->isValid('int', 'abc');
        $this->assertFalse($isValid, "It should return false for an invalid int value");
    }

    public function testValidStringType(): void
    {
        $isValid = $this->validator->isValid('string', 'example');
        $this->assertTrue($isValid, "It should return true for a valid string value");
    }

    public function testInvalidStringType(): void
    {
        $isValid = $this->validator->isValid('string', 123);
        $this->assertFalse($isValid, "It should rturn false for an invalid string value");
    }

    public function testPrepareIntValue(): void
    {
        $intValidator = new IntValidator();
        $preparedValue = $intValidator->prepare("123");
        $this->assertSame(123, $preparedValue, "The value should be converted to int");
    }

    public function testPrepareStringValue(): void
    {
        $stringValidator = new StringValidator();
        $preparedValue = $stringValidator->prepare(123);
        $this->assertSame("123", $preparedValue, "The value should be converted to string");
    }

    public function testUnknownTypeShouldReturnFalse(): void
    {
        $isValid = $this->validator->isValid('unknownType', 'example');
        $this->assertFalse($isValid, "It should return false for an unknown type");
    }
}
