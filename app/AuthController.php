<?php

class AuthController
{
    public static function showLogin(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }
        render('login', ['title' => 'Login']);
    }

    public static function login(): void
    {
        Auth::checkCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($email, $password)) {
            redirect('dashboard');
        }

        flash('Wrong email or password.', 'error');
        redirect('login');
    }

    public static function logout(): void
    {
        Auth::logout();
        redirect('login');
    }

    // after login, send people to their own home page
    public static function dashboard(): void
    {
        Auth::needLogin();
        redirect(Auth::role() === 'manager' ? 'manager/dashboard' : 'employee/dashboard');
    }
}
