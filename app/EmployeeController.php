<?php

class EmployeeController
{
    public static function dashboard(): void
    {
        Auth::needRole('employee');

        $columns = array_fill_keys(array_keys(Tasks::STATUSES), []);
        $overdueCount = 0;

        foreach (Tasks::forEmployee(Auth::id()) as $task) {
            $columns[$task['status']][] = $task;
            if (Tasks::isOverdue($task)) {
                $overdueCount++;
            }
        }

        render('employee_dashboard', [
            'title'        => 'My Tasks',
            'columns'      => $columns,
            'overdueCount' => $overdueCount,
        ]);
    }

    public static function updateStatus(): void
    {
        Auth::needRole('employee');
        Auth::checkCsrf();

        $taskId = (int) ($_POST['task_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!isset(Tasks::STATUSES[$status])) {
            flash('Invalid status.', 'error');
            redirect('employee/dashboard');
        }

        if (Tasks::updateStatus($taskId, Auth::id(), $status)) {
            flash('Task updated.');
        } else {
            flash('Task not found.', 'error');
        }
        redirect('employee/dashboard');
    }

    public static function updates(): void
    {
        Auth::needRole('employee');

        $updates = DailyUpdates::forUser(Auth::id());
        $today   = date('Y-m-d');

        // list is newest first, so if first one is today then prefill the form with it
        $current = null;
        if ($updates && $updates[0]['update_date'] === $today) {
            $current = $updates[0];
        }

        render('employee_updates', [
            'title'   => 'Daily Update',
            'today'   => $today,
            'current' => $current,
            'updates' => $updates,
        ]);
    }

    public static function saveUpdate(): void
    {
        Auth::needRole('employee');
        Auth::checkCsrf();

        $summary  = trim($_POST['work_summary'] ?? '');
        $blockers = trim($_POST['blockers'] ?? '');

        if ($summary === '') {
            flash('Write what you worked on today first.', 'error');
            redirect('employee/updates');
        }

        if (mb_strlen($summary) > 5000 || mb_strlen($blockers) > 2000) {
            flash('Update is too long, please shorten it.', 'error');
            redirect('employee/updates');
        }

        DailyUpdates::save(Auth::id(), date('Y-m-d'), $summary, $blockers);
        flash("Today's update saved.");
        redirect('employee/updates');
    }
}
