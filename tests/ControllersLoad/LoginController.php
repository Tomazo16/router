<?php 

namespace Tomazo\TestRouter\ControllersLoad;

use Tomazo\Router\Attribute\Route;

class LoginController
{
    #[Route('/login', name: 'login')]
    public function login() {}

    #[Route('/logout', name: 'logout')]
    public function logout() {}
}