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

        $status   = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
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
            'task'      => null,
            'employees' => Users::employees(),
        ]);
    }

    public static function createTask(): void
    {
        Auth::needRole('manager');
        Auth::checkCsrf();

        [$data, $errors] = self::readTaskForm();

        if ($errors) {
            flash(implode(' ', $errors), 'error');
            redirect('manager/task/new');
        }

        $data['assigned_by'] = Auth::id();
        Tasks::create($data);

        flash('Task assigned.');
        redirect('manager/tasks');
    }

    public static function editTask(): void
    {
        Auth::needRole('manager');

        $task = Tasks::find((int) ($_GET['id'] ?? 0));
        if (!$task) {
            flash('Task not found.', 'error');
            redirect('manager/tasks');
        }

        render('manager_task_form', [
            'title'     => 'Edit Task',
            'task'      => $task,
            'employees' => Users::employees(),
        ]);
    }

    public static function updateTask(): void
    {
        Auth::needRole('manager');
        Auth::checkCsrf();

        $id = (int) ($_POST['task_id'] ?? 0);
        if (!Tasks::find($id)) {
            flash('Task not found.', 'error');
            redirect('manager/tasks');
        }

        [$data, $errors] = self::readTaskForm();

        if ($errors) {
            flash(implode(' ', $errors), 'error');
            redirect('manager/task/edit&id=' . $id);
        }

        Tasks::update($id, $data);

        flash('Task updated.');
        redirect('manager/tasks');
    }

    public static function deleteTask(): void
    {
        Auth::needRole('manager');
        Auth::checkCsrf();

        $id = (int) ($_POST['task_id'] ?? 0);

        if (Tasks::find($id)) {
            Tasks::delete($id);
            flash('Task deleted.');
        } else {
            flash('Task not found.', 'error');
        }
        redirect('manager/tasks');
    }

    // used by both create and update
    // returns [cleaned fields, error messages], empty errors means ok
    private static function readTaskForm(): array
    {
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'assigned_to' => (int) ($_POST['assigned_to'] ?? 0),
            'priority'    => $_POST['priority'] ?? '',
            'due_date'    => trim($_POST['due_date'] ?? ''),
        ];

        $errors = [];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        } elseif (mb_strlen($data['title']) > 200) {
            $errors[] = 'Title is too long (max 200 characters).';
        }

        if (mb_strlen($data['description']) > 2000) {
            $errors[] = 'Description is too long (max 2000 characters).';
        }

        $assignee = Users::find($data['assigned_to']);
        if (!$assignee || $assignee['role'] !== 'employee') {
            $errors[] = 'Pick an employee.';
        }

        if (!in_array($data['priority'], Tasks::PRIORITIES, true)) {
            $errors[] = 'Invalid priority.';
        }

        if ($data['due_date'] !== '' && !valid_date($data['due_date'])) {
            $errors[] = 'Due date is not valid.';
        }

        return [$data, $errors];
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
