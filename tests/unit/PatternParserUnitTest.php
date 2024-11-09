<?php 

use PHPUnit\Framework\TestCase;
use Tomazo\TestRouter\Controllers\CheckAttrController;
use Tomazo\Router\Utilities\PatternParser;

class PatternParserUnitTest extends TestCase
{
    public function testRouteParser(): void
    {
        $method = new \ReflectionMethod(CheckAttrController::class, 'showDetails');
        $path = '/test/show/Tom/details/11';
        $routePattern = '/test/show/{name}/details/{param}';

        
        $params = PatternParser::parse($path, $routePattern);

        //check whether the arguments are valid
        $this->assertEquals(['name' => 'Tom', 'param' => '11'], $params);

    }
}