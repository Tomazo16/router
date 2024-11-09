<?php 

namespace Tomazo\Router\Utilities;

class PatternParser
{
    public static function parse(string $path, string $routePattern): ?array
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', str_replace('/', '\/', $routePattern));
        if (!preg_match('/^' . $pattern . '$/', $path, $matches)) {
            return NULL;
        }

        // Return matched parameters as an associative array
        preg_match_all('/\{(\w+)\}/', $routePattern, $paramNames);
        array_shift($matches);
        return array_combine($paramNames[1], $matches);
    }
}