<?php 

namespace Tomazo\Router\Model;

class Route
{
    public function __construct(
        private string $name, 
        private array $method,
        private string $route, 
        private array $action
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getMethod(): array
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getAction(): array
    {
        return $this->action;
    }
}