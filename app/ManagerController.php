<?php

class ManagerController
{
    public static function dashboard(): void
    {
        Auth::needRole('manager');

        render('manager_dashboard', [
            'title'       => 'Dashboard',
            'counts'      => Tasks::stats(),
            'updates'     => DailyUpdates::latest(),
            'performance' => Performance::report(),
        ]);
    }

    public static function tasks(): void
    {
        Auth::needRole('manager');

        $status   = $_GET['status'] ?? '';
        $employee = (int) ($_GET['employee'] ?? 0);

        render('manager_tasks', [
            'title'     => 'All Tasks',
            'tasks'     => Tasks::search($status, $employee ?: null),
            'employees' => Users::employees(),
            'status'    => $status,
            'employee'  => $employee,
        ]);
    }

    public static function newTask(): void
    {
        Auth::needRole('manager');

        render('manager_task_form', [
            'title'     => 'Assign Task',
            'employees' => Users::employees(),
        ]);
    }

    public static function createTask(): void
    {
        Auth::needRole('manager');
        Auth::checkCsrf();

        $title    = trim($_POST['title'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';
        $dueDate  = trim($_POST['due_date'] ?? '');
        $assignee = Users::find((int) ($_POST['assigned_to'] ?? 0));

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if (!$assignee || $assignee['role'] !== 'employee') {
            $errors[] = 'Pick an employee.';
        }
        if (!in_array($priority, Tasks::PRIORITIES, true)) {
            $errors[] = 'Invalid priority.';
        }
        if ($dueDate !== '' && !valid_date($dueDate)) {
            $errors[] = 'Due date is not valid.';
        }

        if ($errors) {
            flash(implode(' ', $errors), 'error');
            redirect('manager/task/new');
        }

        Tasks::create([
            'title'       => $title,
            'description' => trim($_POST['description'] ?? ''),
            'assigned_to' => $assignee['id'],
            'assigned_by' => Auth::id(),
            'priority'    => $priority,
            'due_date'    => $dueDate,
        ]);

        flash('Task assigned to ' . $assignee['name'] . '.');
        redirect('manager/tasks');
    }

    public static function reports(): void
    {
        Auth::needRole('manager');

        render('manager_reports', [
            'title'       => 'Reports',
            'performance' => Performance::report(),
        ]);
    }
}
