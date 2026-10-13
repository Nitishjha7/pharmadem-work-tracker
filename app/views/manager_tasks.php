<h1>All Tasks</h1>

<form method="get" class="filters">
    <input type="hidden" name="r" value="manager/tasks">

    <select name="status" onchange="this.form.submit()">
        <option value="">All statuses</option>
        <?php foreach (Tasks::STATUSES + ['overdue' => 'Overdue'] as $key => $label): ?>
            <option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="employee" onchange="this.form.submit()">
        <option value="">All employees</option>
        <?php foreach ($employees as $emp): ?>
            <option value="<?= (int) $emp['id'] ?>" <?= $employee === (int) $emp['id'] ? 'selected' : '' ?>>
                <?= esc($emp['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <a class="link" href="index.php?r=manager/tasks">Reset</a>
</form>

<table class="table">
    <thead>
        <tr><th>Task</th><th>Employee</th><th>Priority</th><th>Status</th><th>Due</th></tr>
    </thead>
    <tbody>
    <?php if (!$tasks): ?>
        <tr><td colspan="5" class="muted">No tasks found.</td></tr>
    <?php endif; ?>

    <?php foreach ($tasks as $t): ?>
        <?php $overdue = Tasks::isOverdue($t); ?>
        <tr class="<?= $overdue ? 'row-overdue' : '' ?>">
            <td>
                <strong><?= esc($t['title']) ?></strong>
                <?php if ($t['description']): ?>
                    <div class="muted small"><?= esc($t['description']) ?></div>
                <?php endif; ?>
            </td>
            <td><?= esc($t['employee_name']) ?></td>
            <td><span class="pill pill-<?= esc($t['priority']) ?>"><?= esc($t['priority']) ?></span></td>
            <td><?= esc(Tasks::STATUSES[$t['status']]) ?></td>
            <td>
                <?php if ($t['due_date']): ?>
                    <?= esc($t['due_date']) ?>
                    <?php if ($overdue): ?><span class="due-over">(overdue)</span><?php endif; ?>
                <?php else: ?>
                    <span class="muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
