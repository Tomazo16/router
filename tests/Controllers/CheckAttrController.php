<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Router\Attribute\Route;

class CheckAttrController
{
    #[Route('/test/show/{id}', name: 'show')]
    public function show(int $id) {
        return 'hello world';
    }

    #[Route('/test/show/{name}/details/{param}', name: 'showDetails')]
    public function showDetails(string $name, int $param) {
        return $this;
    }

    #[Route('/test/show/{id}/{param}', name: 'profile')]
    public function profile(int $id, int $param) {}
}