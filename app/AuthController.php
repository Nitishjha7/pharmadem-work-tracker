<?php

class AuthController
{
    public static function showLogin(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }
        view('login', ['title' => 'Login']);
    }

    public static function login(): void
    {
        Auth::verifyCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($email, $password)) {
            redirect('dashboard');
        }

        set_flash('Wrong email or password.', 'error');
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
        Auth::requireLogin();
        redirect(Auth::role() === 'manager' ? 'manager/dashboard' : 'employee/dashboard');
    }
}
