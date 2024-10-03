<?php 

namespace Tomazo\Router\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        private string $path,
        private string $name,
        private array $methods = ['GET']
    )
    {
        
    }

    public function getPath(): string
    {
        return $this->path;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }
}