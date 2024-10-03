<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Router\Attribute\Route;

class IndexController
{
    #[Route('/index', name: 'index')]
    public function index() {}
}