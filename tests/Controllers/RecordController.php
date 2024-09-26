<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Router\Attribute\Route;

class RecordController
{
    #[Route('/test/show/{id}', name: 'show')]
    public function show(int $id) {}
}