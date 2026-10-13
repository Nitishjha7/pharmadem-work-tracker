<?php

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        // fresh session id after login
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'   => (int) $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
        ];
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function needLogin(): void
    {
        if (!self::check()) {
            redirect('login');
        }
    }

    // wrong role = 403, not logged in = login page
    public static function needRole(string $role): void
    {
        self::needLogin();

        if (self::role() !== $role) {
            http_response_code(403);
            exit('You are not allowed to open this page.');
        }
    }

    public static function csrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function checkCsrf(): void
    {
        $sent = $_POST['csrf'] ?? '';

        if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
            http_response_code(419);
            exit('Form expired, please go back and try again.');
        }
    }
}
