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
- edit or delete a task (status stays with the employee)
- list all tasks, filter by status (including overdue) or by employee
- performance report per employee, with the score breakdown shown next to each number

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

## Why it is built this way

- **Score is simple on purpose.** A manager should be able to look at the numbers and see why someone got 78. The report prints the working (`4/5 done = 48.0 + 3/4 on time = 30.0`) instead of just the final number.
- **Completion weighs more than punctuality (60/40).** Finishing work matters more than finishing it early. The weights are constants in `Performance.php` if the team wants to change them.
- **No tasks means score 0, not 100.** Otherwise a new joiner would top the table.
- **Overdue is defined once** (`Tasks::OVERDUE_COND`). The dashboard count, the filter and the report all use it, so they cannot disagree. `Tasks::isOverdue()` is the same rule for a single row in PHP.
- **Managers edit the task, employees own the status.** Edit does not touch `status` or `completed_at`, so a manager cannot quietly change someone's completion record.
- **Deleting a task removes it from the score.** There is no soft delete, this is a deliberate shortcut. A real system would probably archive instead.
- **Score maths is a separate function** (`Performance::calculate`) with no database in it, which is why it can be tested without MySQL.
- **No framework.** For this size a small router and PDO is enough and everything stays readable. Past a handful more screens I would move to Laravel or Slim.

## Setup

1. Put the project in your web root (for XAMPP, `htdocs/pharmadem`) or anywhere you like.
2. Import the database:
   ```
   mysql -u root -p < sql/schema.sql
   ```
3. Check the DB details in `config.php`. Defaults are `root` with an empty password on localhost. You can also set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` as environment variables. The timezone is also set there (`Asia/Kolkata` by default, or `APP_TZ`). PHP and MySQL are both put on it, because "today" decides what is overdue.
4. Start the app with the document root set to `public/`:
   ```
   cd public
   php -S localhost:8000
   ```
   and open http://localhost:8000

## Tests

```
php tests/run.php
```

Plain PHP, no composer. Covers the score calculation, rating boundaries (79/80, 59/60, 39/40), the no-tasks case, the overdue rule and the small helpers. Anything that needs the database is checked by hand through the UI.

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
tests/run.php         unit tests
```

## Security

- passwords stored with `password_hash`
- all queries use prepared statements
- output escaped with `htmlspecialchars`
- CSRF token on every POST form
- session id regenerated on login
- every manager/employee page checks the role, and employees can only update their own tasks
- delete is a POST with a CSRF token, never a plain link

## Database

- `users`: name, email, password hash, role (manager or employee)
- `tasks`: title, description, assigned_to, assigned_by, priority, status, due_date, completed_at
- `daily_updates`: user, date, work summary, blockers. Unique on (user, date), so saving twice in a day updates the same row.

## Not done yet

Pagination, task comments, email reminders and CSV export of reports.
