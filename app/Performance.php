<?php

// Score = 60% completion rate + 40% on-time rate
//   completion rate = completed / assigned
//   on-time rate    = completed on or before due date / completed
// A completed task with no due date counts as on time.
// Someone with no tasks gets 0, not 100.
class Performance
{
    public static function perEmployee(): array
    {
        $sql = "SELECT u.id, u.name,
                       COUNT(t.id) AS total,
                       COALESCE(SUM(t.status = 'completed'), 0) AS completed,
                       COALESCE(SUM(" . Tasks::OVERDUE . "), 0) AS overdue,
                       COALESCE(SUM(t.status = 'completed'
                                    AND (t.due_date IS NULL OR DATE(t.completed_at) <= t.due_date)), 0) AS on_time
                FROM users u
                LEFT JOIN tasks t ON t.assigned_to = u.id
                WHERE u.role = 'employee'
                GROUP BY u.id, u.name
                ORDER BY u.name";

        $result = [];
        foreach (Database::connection()->query($sql)->fetchAll() as $row) {
            $total     = (int) $row['total'];
            $completed = (int) $row['completed'];
            $onTime    = (int) $row['on_time'];

            $completionRate = $total ? $completed / $total : 0;
            $onTimeRate     = $completed ? $onTime / $completed : 0;
            $score          = (int) round($completionRate * 60 + $onTimeRate * 40);

            $result[] = [
                'id'              => (int) $row['id'],
                'name'            => $row['name'],
                'total'           => $total,
                'completed'       => $completed,
                'on_time'         => $onTime,
                'overdue'         => (int) $row['overdue'],
                'completion_rate' => (int) round($completionRate * 100),
                'score'           => $score,
                'rating'          => self::rating($score),
            ];
        }

        return $result;
    }

    public static function rating(int $score): string
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Average';
        return 'Needs improvement';
    }
}
