<?php

class DailyUpdates
{
    // one row per user per day, saving again on the same day overwrites it
    public static function save(int $userId, string $date, string $summary, string $blockers): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO daily_updates (user_id, update_date, work_summary, blockers)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE work_summary = VALUES(work_summary), blockers = VALUES(blockers)'
        );
        $stmt->execute([$userId, $date, $summary, $blockers !== '' ? $blockers : null]);
    }

    public static function forUser(int $userId, int $limit = 30): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM daily_updates WHERE user_id = ? ORDER BY update_date DESC LIMIT ?'
        );
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function latest(int $limit = 10): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT d.*, u.name AS employee_name
             FROM daily_updates d
             JOIN users u ON u.id = d.user_id
             ORDER BY d.update_date DESC, d.id DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
