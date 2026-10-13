<?php
$editing = $task !== null;
$action  = $editing ? 'manager/task/update' : 'manager/task/create';
?>
<h1><?= $editing ? 'Edit Task' : 'Assign a Task' ?></h1>

<form method="post" action="index.php?r=<?= $action ?>" class="form-card">
    <input type="hidden" name="csrf" value="<?= esc(Auth::csrf()) ?>">
    <?php if ($editing): ?>
        <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">
    <?php endif; ?>

    <label>Title <span class="req">*</span></label>
    <input type="text" name="title" required maxlength="200" value="<?= esc($task['title'] ?? '') ?>">

    <label>Description</label>
    <textarea name="description" rows="3"><?= esc($task['description'] ?? '') ?></textarea>

    <div class="grid-2">
        <div>
            <label>Assign to <span class="req">*</span></label>
            <select name="assigned_to" required>
                <option value="">Select employee</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= (int) $emp['id'] ?>"
                        <?= (int) ($task['assigned_to'] ?? 0) === (int) $emp['id'] ? 'selected' : '' ?>>
                        <?= esc($emp['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Priority</label>
            <select name="priority">
                <?php foreach (Tasks::PRIORITIES as $p): ?>
                    <option value="<?= $p ?>" <?= $p === ($task['priority'] ?? 'medium') ? 'selected' : '' ?>>
                        <?= ucfirst($p) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <label>Due date</label>
    <input type="date" name="due_date" value="<?= esc($task['due_date'] ?? '') ?>">

    <button type="submit"><?= $editing ? 'Save changes' : 'Assign' ?></button>
    <?php if ($editing): ?>
        <a class="link cancel" href="index.php?r=manager/tasks">Cancel</a>
    <?php endif; ?>
</form>
