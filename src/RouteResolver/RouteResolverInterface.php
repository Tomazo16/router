<?php 

namespace Tomazo\Router\RouteResolver;

interface RouteResolverInterface
{
    public function resolveRoute(array $route): string;
}