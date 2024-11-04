<?php 

namespace Tomazo\TestRouter\ControllersLoad;

use Tomazo\Router\Attribute\Route;

class IndexController
{
    #[Route('/index', name: 'index')]
    public function index() {}
}