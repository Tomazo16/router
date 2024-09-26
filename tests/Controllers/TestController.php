<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Router\Attribute\Route;

class TestController
{
    #[Route('/t/index', name: 'testIndex')]
    public function index() {}

    #[Route('/test', name: 'test', methods: ['POST'])]
    public function test() {}

    #[Route('/protected', name: 'protect')]
    protected function protect() {}

    #[Route('/priv', name: 'priv')]
    private function priv() {}
}