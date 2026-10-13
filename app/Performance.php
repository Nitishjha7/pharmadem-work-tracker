<?php

// Score = 60% completion rate + 40% on-time rate
//   completion rate = completed / assigned
//   on-time rate    = completed on or before due date / completed
// A completed task with no due date counts as on time.
// Someone with no tasks gets 0, not 100.
class Performance
{
    const COMPLETION_WEIGHT = 60;
    const ON_TIME_WEIGHT    = 40;

    public static function report(): array
    {
        $sql = "SELECT u.id, u.name,
                       COUNT(t.id) AS total,
                       COALESCE(SUM(t.status = 'completed'), 0) AS completed,
                       COALESCE(SUM(" . Tasks::OVERDUE_COND . "), 0) AS overdue,
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

            $result[] = [
                'id'        => (int) $row['id'],
                'name'      => $row['name'],
                'total'     => $total,
                'completed' => $completed,
                'on_time'   => $onTime,
                'overdue'   => (int) $row['overdue'],
            ] + self::calculate($total, $completed, $onTime);
        }

        return $result;
    }

    // Pure maths, no database. Kept separate so it can be tested on its own.
    public static function calculate(int $total, int $completed, int $onTime): array
    {
        $completionRate = $total > 0 ? $completed / $total : 0;
        $onTimeRate     = $completed > 0 ? $onTime / $completed : 0;

        $completionPoints = $completionRate * self::COMPLETION_WEIGHT;
        $onTimePoints     = $onTimeRate * self::ON_TIME_WEIGHT;
        $score            = (int) round($completionPoints + $onTimePoints);

        return [
            'completion_rate'   => (int) round($completionRate * 100),
            'completion_points' => round($completionPoints, 1),
            'on_time_points'    => round($onTimePoints, 1),
            'score'             => $score,
            'rating'            => self::ratingFor($score),
        ];
    }

    public static function ratingFor(int $score): string
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Average';
        return 'Needs improvement';
    }
}
