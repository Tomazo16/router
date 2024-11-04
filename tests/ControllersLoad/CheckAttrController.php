<?php 

namespace Tomazo\TestRouter\ControllersLoad;

use Tomazo\Router\Attribute\Route;

class CheckAttrController
{
    #[Route('/test/show/{id}', name: 'show')]
    public function show(int $id) {}

    #[Route('/test/show/{name}/details/{param}', name: 'showDetails')]
    public function showDetails(string $name, int $param) {
        return $this;
    }

    #[Route('/test/show/{id}/{param}', name: 'profile')]
    public function profile(int $id, int $param) {}
}