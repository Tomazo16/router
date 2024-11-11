<?php 

namespace Tomazo\Router\RouteResolver;

use Tomazo\Router\Model\Route;

interface RouteResolverInterface
{
    public function resolveRoute(Route $route): array;
    public function callAction(Route $route): mixed;
    public function getRoutePaths(Route $route): string;
}