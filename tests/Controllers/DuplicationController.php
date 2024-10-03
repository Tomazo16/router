<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Router\Attribute\Route;

class DuplicationController
{
    #[Route('/a', name: 'dupl')]
    public function a() {}

    #[Route('/b', name: 'dupl')]
    public function b() {}
}