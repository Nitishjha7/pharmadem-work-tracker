<?php

class Users
{
    public static function employees(): array
    {
        $sql = "SELECT id, name, email FROM users WHERE role = 'employee' ORDER BY name";
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
