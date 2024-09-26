<?php 

namespace Tomazo\TestRouter\Controllers;

use Tomazo\Route\Attribute\Route;

class LoginController
{
    #[Route('/login', name: 'login')]
    public function login() {}

    #[Route('/logout', name: 'logout')]
    public function logout() {}
}