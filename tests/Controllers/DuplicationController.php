<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Router\Attribute\Route;

class DuplicationController
{
    #[Route('/a', name: 'a')]
    public function a() {}

    #[Route('/a', name: 'a')]
    public function b() {}
}