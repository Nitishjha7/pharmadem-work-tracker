<h1>Daily Update</h1>
<p class="muted">Write what you did today. You can come back and edit it until the day ends.</p>

<div class="open-tasks">
    <h2>Your open tasks (<?= count($openTasks) ?>)</h2>
    <?php if (!$openTasks): ?>
        <p class="muted small">Nothing open right now.</p>
    <?php endif; ?>
    <ul>
        <?php foreach ($openTasks as $t): ?>
            <li>
                <?= esc($t['title']) ?>
                <span class="muted small">
                    - <?= esc(Tasks::STATUSES[$t['status']]) ?><?php if ($t['due_date']): ?>, due <?= esc($t['due_date']) ?><?php endif; ?>
                </span>
                <?php if (Tasks::isOverdue($t)): ?><span class="due-over small">overdue</span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<form method="post" action="index.php?r=daily/save" class="form-card">
    <input type="hidden" name="csrf" value="<?= esc(Auth::csrf()) ?>">

    <label>What did you work on today? <span class="req">*</span></label>
    <textarea name="work_summary" rows="4" required><?= esc($current['work_summary'] ?? '') ?></textarea>

    <label>To-do next (planned / still pending)</label>
    <textarea name="plan" rows="2"><?= esc($current['plan'] ?? '') ?></textarea>

    <label>Blockers (if any)</label>
    <textarea name="blockers" rows="2"><?= esc($current['blockers'] ?? '') ?></textarea>

    <button type="submit"><?= $current ? 'Update' : 'Save' ?> entry for <?= esc($today) ?></button>
</form>

<h2 class="section-head">Past updates</h2>

<?php if (!$updates): ?>
    <p class="muted">Nothing posted yet.</p>
<?php endif; ?>

<div class="update-list">
    <?php foreach ($updates as $u): ?>
        <article class="update-item">
            <div class="update-date"><?= esc($u['update_date']) ?></div>
            <div class="update-body">
                <p><?= nl2br(esc($u['work_summary'])) ?></p>
                <?php if ($u['plan']): ?>
                    <p><strong>To-do next:</strong> <?= nl2br(esc($u['plan'])) ?></p>
                <?php endif; ?>
                <?php if ($u['blockers']): ?>
                    <p class="blocker"><strong>Blockers:</strong> <?= nl2br(esc($u['blockers'])) ?></p>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</div>
