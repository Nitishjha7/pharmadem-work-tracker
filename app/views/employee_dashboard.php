<?php
// the button that moves a task forward from each status
$moveTo = ['todo' => 'in_progress', 'in_progress' => 'completed', 'pending' => 'in_progress'];
?>
<h1>My Tasks</h1>

<?php if ($overdueCount > 0): ?>
    <div class="flash flash-error">You have <?= $overdueCount ?> overdue task<?= $overdueCount > 1 ? 's' : '' ?>.</div>
<?php endif; ?>

<div class="board">
    <?php foreach (Tasks::STATUSES as $key => $label): ?>
        <section class="column">
            <h2><?= esc($label) ?> <span class="count"><?= count($columns[$key]) ?></span></h2>

            <?php if (!$columns[$key]): ?>
                <p class="muted small">Nothing here.</p>
            <?php endif; ?>

            <?php foreach ($columns[$key] as $task): ?>
                <?php $overdue = Tasks::isOverdue($task); ?>
                <article class="card<?= $overdue ? ' card-overdue' : '' ?>">
                    <div class="card-top">
                        <strong><?= esc($task['title']) ?></strong>
                        <span class="pill pill-<?= esc($task['priority']) ?>"><?= esc($task['priority']) ?></span>
                    </div>

                    <?php if ($task['description']): ?>
                        <p class="muted small"><?= esc($task['description']) ?></p>
                    <?php endif; ?>

                    <div class="card-meta">
                        <?php if ($task['due_date']): ?>
                            <span class="<?= $overdue ? 'due-over' : '' ?>">
                                Due <?= esc($task['due_date']) ?><?= $overdue ? ' (overdue)' : '' ?>
                            </span>
                        <?php else: ?>
                            No due date
                        <?php endif; ?>
                    </div>

                    <?php if ($task['status'] === 'completed'): ?>
                        <div class="done-at">Completed on <?= esc($task['completed_at']) ?></div>
                    <?php else: ?>
                        <form method="post" action="index.php?r=task/status" class="inline-actions">
                            <input type="hidden" name="csrf" value="<?= esc(Auth::csrf()) ?>">
                            <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">

                            <?php if (isset($moveTo[$task['status']])): ?>
                                <?php $next = $moveTo[$task['status']]; ?>
                                <button name="status" value="<?= esc($next) ?>" class="btn-sm">
                                    Move to <?= esc(Tasks::STATUSES[$next]) ?>
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
