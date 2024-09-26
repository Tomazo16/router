<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Route\Attribute\Route;

class IndexController
{
    #[Route('/index', name: 'index')]
    public function index() {}
}