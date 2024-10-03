<?php 

namespace Tomazo\Router\RouteLoader;

interface RouteLoaderInterface
{
    public function loadRoute(): array;
}