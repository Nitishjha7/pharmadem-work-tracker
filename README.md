# PharmaDEM Work Tracker

Employee daily work update and performance evaluation system, built with plain PHP and MySQL for the PharmaDEM assignment.

Managers assign tasks and see how each employee is doing. Employees update their task status and post a short update every day.

## Stack

- PHP 8 (no framework, PDO for the database)
- MySQL / MariaDB
- Plain CSS

## What it does

Manager
- dashboard with task counts (total, completed, open, overdue), team scores and the latest daily updates
- assign a task to an employee with priority and due date
- list all tasks, filter by status (including overdue) or by employee
- performance report per employee

Employee
- own tasks shown in four columns: To Do, In Progress, Pending, Completed
- change a task's status, completion time is saved automatically
- one daily update per day (work done + blockers), can be edited the same day

## Overdue and performance score

A task is overdue if it is not completed and its due date is before today. The rule is defined once in `Tasks::OVERDUE_COND` and used by every query.

```
score = 60% * completion rate + 40% * on-time rate

completion rate = completed tasks / assigned tasks
on-time rate    = tasks completed on or before the due date / completed tasks
```

A completed task with no due date counts as on time. An employee with no tasks gets 0.

Rating: 80+ Excellent, 60+ Good, 40+ Average, below 40 Needs improvement.

## Setup

1. Put the project in your web root (for XAMPP, `htdocs/pharmadem`) or anywhere you like.
2. Import the database:
   ```
   mysql -u root -p < sql/schema.sql
   ```
3. Check the DB details in `config.php`. Defaults are `root` with an empty password on localhost. You can also set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` as environment variables.
4. Start the app with the document root set to `public/`:
   ```
   cd public
   php -S localhost:8000
   ```
   and open http://localhost:8000

## Demo logins

Password for all accounts is `password123`.

| Role     | Email                |
|----------|----------------------|
| Manager  | manager@pharmadem.in |
| Employee | arjun@pharmadem.in   |
| Employee | sneha@pharmadem.in   |

## Folder layout

```
config.php            database settings
sql/schema.sql        tables and demo data
public/index.php      entry point and routes
public/assets/        css
app/
  Database.php        PDO connection
  Auth.php            login, roles, CSRF
  helpers.php         esc(), redirect(), flash messages, render()
  Users.php, Tasks.php, DailyUpdates.php, Performance.php    database queries
  *Controller.php     request handling
  views/              page templates
```

## Security

- passwords stored with `password_hash`
- all queries use prepared statements
- output escaped with `htmlspecialchars`
- CSRF token on every POST form
- session id regenerated on login
- every manager/employee page checks the role, and employees can only update their own tasks

## Database

- `users`: name, email, password hash, role (manager or employee)
- `tasks`: title, description, assigned_to, assigned_by, priority, status, due_date, completed_at
- `daily_updates`: user, date, work summary, blockers. Unique on (user, date), so saving twice in a day updates the same row.

## Not done yet

Pagination, editing or deleting tasks, task comments, email reminders and CSV export of reports.
