<h1>Assign a Task</h1>

<form method="post" action="index.php?r=manager/task/create" class="form-card">
    <input type="hidden" name="csrf" value="<?= esc(Auth::csrf()) ?>">

    <label>Title <span class="req">*</span></label>
    <input type="text" name="title" required maxlength="200">

    <label>Description</label>
    <textarea name="description" rows="3"></textarea>

    <div class="grid-2">
        <div>
            <label>Assign to <span class="req">*</span></label>
            <select name="assigned_to" required>
                <option value="">Select employee</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= (int) $emp['id'] ?>"><?= esc($emp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Priority</label>
            <select name="priority">
                <?php foreach (Tasks::PRIORITIES as $p): ?>
                    <option value="<?= $p ?>" <?= $p === 'medium' ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <label>Due date</label>
    <input type="date" name="due_date">

    <button type="submit">Assign</button>
</form>
