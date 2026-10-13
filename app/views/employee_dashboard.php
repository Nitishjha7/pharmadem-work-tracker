<?php
// the button that moves a task forward from each status
$nextStatus = ['todo' => 'in_progress', 'in_progress' => 'completed', 'pending' => 'in_progress'];
?>
<h1>My Tasks</h1>

<?php if ($overdueCount > 0): ?>
    <div class="flash flash-error">You have <?= $overdueCount ?> overdue task<?= $overdueCount > 1 ? 's' : '' ?>.</div>
<?php endif; ?>

<div class="board">
    <?php foreach (Tasks::STATUSES as $key => $label): ?>
        <section class="column">
            <h2><?= e($label) ?> <span class="count"><?= count($buckets[$key]) ?></span></h2>

            <?php if (!$buckets[$key]): ?>
                <p class="muted small">Nothing here.</p>
            <?php endif; ?>

            <?php foreach ($buckets[$key] as $task): ?>
                <?php $overdue = Tasks::isOverdue($task); ?>
                <article class="card<?= $overdue ? ' card-overdue' : '' ?>">
                    <div class="card-top">
                        <strong><?= e($task['title']) ?></strong>
                        <span class="pill pill-<?= e($task['priority']) ?>"><?= e($task['priority']) ?></span>
                    </div>

                    <?php if ($task['description']): ?>
                        <p class="muted small"><?= e($task['description']) ?></p>
                    <?php endif; ?>

                    <div class="card-meta">
                        <?php if ($task['due_date']): ?>
                            <span class="<?= $overdue ? 'due-over' : '' ?>">
                                Due <?= e($task['due_date']) ?><?= $overdue ? ' (overdue)' : '' ?>
                            </span>
                        <?php else: ?>
                            No due date
                        <?php endif; ?>
                    </div>

                    <?php if ($task['status'] === 'completed'): ?>
                        <div class="done-at">Completed on <?= e($task['completed_at']) ?></div>
                    <?php else: ?>
                        <form method="post" action="index.php?r=task/status" class="inline-actions">
                            <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">
                            <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">

                            <?php if (isset($nextStatus[$task['status']])): ?>
                                <?php $next = $nextStatus[$task['status']]; ?>
                                <button name="status" value="<?= e($next) ?>" class="btn-sm">
                                    Move to <?= e(Tasks::STATUSES[$next]) ?>
                                </button>
                            <?php endif; ?>

                            <?php if ($task['status'] !== 'pending'): ?>
                                <button name="status" value="pending" class="btn-sm btn-ghost">Mark pending</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</div>
