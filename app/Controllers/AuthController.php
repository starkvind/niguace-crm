<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\User;

class AuthController
{
    public function loginForm(): void
    {
        View::render('auth/login', ['title' => 'Acceso'], 'auth');
    }

    public function login(): void
    {
        verify_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Credenciales incorrectas.';
            redirect('/login');
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        redirect('/admin');
    }

    public function logout(): void
    {
        verify_csrf();
        $_SESSION = [];
        session_destroy();
        redirect('/');
    }
}
