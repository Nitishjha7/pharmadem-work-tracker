# PharmaDEM Work Tracker

Employee daily work update and performance tracking, written in plain PHP + MySQL for the PharmaDEM assignment. No framework, just PDO and a small router.

Managers give out tasks and see how everyone is doing. Employees move their tasks forward and post a short update each day.

## What's in it

Manager
- dashboard: task counts, team scores, latest daily updates
- assign a task (priority, due date), edit it later, or delete it
- all tasks list, filter by status (incl. overdue) or employee
- performance report per employee

Employee
- own tasks in To Do / In Progress / Pending / Completed columns
- change status, completion time is stored when a task is marked completed
- daily update with what was done, what's next and blockers. One per day, can be edited the same day. The page also shows the employee's open tasks.

## Setup

1. Import the database (this drops and recreates the `pharmadem` tables):
   ```
   mysql -u root -p < sql/schema.sql
   ```
2. DB details are in `config.php` (root, no password, localhost by default). `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` and `APP_TZ` can also be set as env variables. Timezone defaults to Asia/Kolkata and is applied to both PHP and MySQL, since "today" decides what is overdue.
3. Run it from the `public` folder:
   ```
   cd public
   php -S localhost:8000
   ```

Logins, password is `password123` for all:

- manager@pharmadem.in
- arjun@pharmadem.in
- sneha@pharmadem.in

Tests (no DB needed): `php tests/run.php`

## Overdue and score

A task is overdue when it's not completed and the due date is before today. That condition is written once (`Tasks::OVERDUE_COND`) and reused by the dashboard, the filter and the report, so the numbers always agree.

Score out of 100:

```
60% * (completed / assigned) + 40% * (completed on time / completed)
```

Completed tasks with no due date count as on time. If someone has no tasks the score is 0, otherwise a new joiner would show 100. Rating is Excellent from 80, Good from 60, Average from 40, else Needs improvement.

I went with completion over punctuality (60/40) because getting the work done matters more than getting it done early. The weights are two constants in `Performance.php` if that needs to change. The report also prints the working next to each score (like `4/5 done = 48.0 + 3/4 on time = 30.0`) so a manager can see why a number is what it is.

A few choices worth knowing about:
- manager edit doesn't touch status or completed_at, those belong to the employee
- deleting a task is a real delete, so it also drops out of the score
- the score maths is its own function (`Performance::calculate`) so it can be tested without a database

## Layout

```
config.php          db + timezone settings
sql/schema.sql      tables and demo data
public/index.php    routes
app/                models, controllers, helpers
app/views/          templates
tests/run.php       tests
```

## Security

Prepared statements everywhere, passwords with `password_hash`, output escaped with `htmlspecialchars`, CSRF token on every POST, session id regenerated on login, role check on every manager/employee page, and employees can only update their own tasks.

## Tables

- `users`: name, email, password hash, role
- `tasks`: title, description, assigned_to, assigned_by, priority, status, due_date, completed_at
- `daily_updates`: user, date, work summary, plan, blockers. Unique on (user, date), so saving twice in a day updates the same row.
