<?php

class EmployeeController
{
    public static function dashboard(): void
    {
        Auth::requireRole('employee');

        $buckets = array_fill_keys(array_keys(Tasks::STATUSES), []);
        $overdueCount = 0;

        foreach (Tasks::forEmployee(Auth::id()) as $task) {
            $buckets[$task['status']][] = $task;
            if (Tasks::isOverdue($task)) {
                $overdueCount++;
            }
        }

        view('employee_dashboard', [
            'title'        => 'My Tasks',
            'buckets'      => $buckets,
            'overdueCount' => $overdueCount,
        ]);
    }

    public static function updateStatus(): void
    {
        Auth::requireRole('employee');
        Auth::verifyCsrf();

        $taskId = (int) ($_POST['task_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!isset(Tasks::STATUSES[$status])) {
            set_flash('Invalid status.', 'error');
            redirect('employee/dashboard');
        }

        if (Tasks::updateStatus($taskId, Auth::id(), $status)) {
            set_flash('Task updated.');
        } else {
            set_flash('Task not found.', 'error');
        }
        redirect('employee/dashboard');
    }

    public static function updates(): void
    {
        Auth::requireRole('employee');

        $updates = DailyUpdates::forUser(Auth::id());
        $today   = date('Y-m-d');

        // if today's entry exists, load it into the form so it can be edited
        $current = null;
        if ($updates && $updates[0]['update_date'] === $today) {
            $current = $updates[0];
        }

        view('employee_updates', [
            'title'   => 'Daily Update',
            'today'   => $today,
            'current' => $current,
            'updates' => $updates,
        ]);
    }

    public static function saveUpdate(): void
    {
        Auth::requireRole('employee');
        Auth::verifyCsrf();

        $summary  = trim($_POST['work_summary'] ?? '');
        $blockers = trim($_POST['blockers'] ?? '');

        if ($summary === '') {
            set_flash('Write what you worked on today first.', 'error');
            redirect('employee/updates');
        }

        DailyUpdates::save(Auth::id(), date('Y-m-d'), $summary, $blockers);
        set_flash("Today's update saved.");
        redirect('employee/updates');
    }
}
