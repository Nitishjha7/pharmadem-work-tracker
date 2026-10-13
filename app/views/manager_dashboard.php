<h1>Dashboard</h1>

<div class="stats">
    <div class="stat"><div class="stat-num"><?= $counts['total'] ?></div><div class="stat-label">Total tasks</div></div>
    <div class="stat"><div class="stat-num"><?= $counts['completed'] ?></div><div class="stat-label">Completed</div></div>
    <div class="stat"><div class="stat-num"><?= $counts['open'] ?></div><div class="stat-label">Open</div></div>
    <div class="stat stat-warn"><div class="stat-num"><?= $counts['overdue'] ?></div><div class="stat-label">Overdue</div></div>
</div>

<div class="two-col">
    <section>
        <h2 class="section-head">Team performance</h2>
        <table class="table">
            <thead>
                <tr><th>Employee</th><th>Score</th><th>Rating</th><th>Overdue</th></tr>
            </thead>
            <tbody>
            <?php foreach ($performance as $p): ?>
                <tr>
                    <td><?= esc($p['name']) ?></td>
                    <td><strong><?= $p['score'] ?></strong></td>
                    <td><span class="rating <?= rating_css($p['rating']) ?>"><?= esc($p['rating']) ?></span></td>
                    <td><?= $p['overdue'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><a class="link" href="index.php?r=manager/reports">Full report</a></p>
    </section>

    <section>
        <h2 class="section-head">Latest updates</h2>

        <?php if (!$updates): ?>
            <p class="muted">No updates yet.</p>
        <?php endif; ?>

        <div class="update-list">
            <?php foreach ($updates as $u): ?>
                <article class="update-item">
                    <div class="update-date"><?= esc($u['update_date']) ?></div>
                    <div class="update-body">
                        <strong><?= esc($u['employee_name']) ?></strong>
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
    </section>
</div>
