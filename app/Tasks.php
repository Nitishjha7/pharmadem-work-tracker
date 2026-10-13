<?php

class Tasks
{
    const STATUSES = [
        'todo'        => 'To Do',
        'in_progress' => 'In Progress',
        'pending'     => 'Pending',
        'completed'   => 'Completed',
    ];

    const PRIORITIES = ['low', 'medium', 'high'];

    // Overdue = not completed and due date already passed.
    // Every query that needs "overdue" uses this so the numbers match everywhere.
    // Tables using it must alias tasks as t.
    const OVERDUE_COND = "(t.status <> 'completed' AND t.due_date IS NOT NULL AND t.due_date < CURDATE())";

    public static function isOverdue(array $task): bool
    {
        return $task['status'] !== 'completed'
            && !empty($task['due_date'])
            && $task['due_date'] < date('Y-m-d');
    }

    public static function forEmployee(int $employeeId): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM tasks t
             WHERE t.assigned_to = ?
             ORDER BY t.due_date IS NULL, t.due_date"
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }

    public static function search(?string $status, ?int $employeeId): array
    {
        $sql = "SELECT t.*, u.name AS employee_name
                FROM tasks t
                JOIN users u ON u.id = t.assigned_to
                WHERE 1 = 1";
        $params = [];

        if ($status === 'overdue') {
            $sql .= ' AND ' . self::OVERDUE_COND;
        } elseif (isset(self::STATUSES[$status])) {
            $sql .= ' AND t.status = ?';
            $params[] = $status;
        }

        if ($employeeId) {
            $sql .= ' AND t.assigned_to = ?';
            $params[] = $employeeId;
        }

        $sql .= ' ORDER BY t.due_date IS NULL, t.due_date';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO tasks (title, description, assigned_to, assigned_by, priority, due_date)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?: null,
            $data['assigned_to'],
            $data['assigned_by'],
            $data['priority'],
            $data['due_date'] ?: null,
        ]);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Manager edit. Status and completed_at are left alone, those belong to the employee.
    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE tasks
             SET title = ?, description = ?, assigned_to = ?, priority = ?, due_date = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?: null,
            $data['assigned_to'],
            $data['priority'],
            $data['due_date'] ?: null,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }

    // The assigned_to check means an employee can only touch their own tasks.
    public static function updateStatus(int $taskId, int $employeeId, string $status): bool
    {
        $completedAt = $status === 'completed' ? date('Y-m-d H:i:s') : null;

        $stmt = Database::connection()->prepare(
            'UPDATE tasks SET status = ?, completed_at = ? WHERE id = ? AND assigned_to = ?'
        );
        $stmt->execute([$status, $completedAt, $taskId, $employeeId]);

        // rowCount is 0 both for "not your task" and "nothing changed", so look the row up
        $check = Database::connection()->prepare('SELECT 1 FROM tasks WHERE id = ? AND assigned_to = ?');
        $check->execute([$taskId, $employeeId]);
        return (bool) $check->fetchColumn();
    }

    public static function stats(): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(SUM(t.status = 'completed'), 0) AS completed,
                    COALESCE(SUM(t.status <> 'completed'), 0) AS open,
                    COALESCE(SUM(" . self::OVERDUE_COND . "), 0) AS overdue
                FROM tasks t";

        return array_map('intval', Database::connection()->query($sql)->fetch());
    }
}
