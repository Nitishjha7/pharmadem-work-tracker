<h1>Performance Reports</h1>
<p class="muted">
    Score = 60% completion rate + 40% on-time rate. Overdue tasks are listed separately.
</p>

<table class="table">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Assigned</th>
            <th>Completed</th>
            <th>On time</th>
            <th>Overdue</th>
            <th>Completion</th>
            <th>Score</th>
            <th>How the score is made</th>
            <th>Rating</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($performance as $p): ?>
        <tr>
            <td><strong><?= esc($p['name']) ?></strong></td>
            <td><?= $p['total'] ?></td>
            <td><?= $p['completed'] ?></td>
            <td><?= $p['on_time'] ?></td>
            <td><?= $p['overdue'] ?></td>
            <td>
                <div class="bar"><div class="bar-fill" style="width: <?= $p['completion_rate'] ?>%"></div></div>
                <span class="small"><?= $p['completion_rate'] ?>%</span>
            </td>
            <td><strong><?= $p['score'] ?></strong></td>
            <td class="small muted">
                <?= $p['completed'] ?>/<?= $p['total'] ?> done = <?= number_format($p['completion_points'], 1) ?>
                + <?= $p['on_time'] ?>/<?= $p['completed'] ?> on time = <?= number_format($p['on_time_points'], 1) ?>
            </td>
            <td><span class="rating <?= rating_css($p['rating']) ?>"><?= esc($p['rating']) ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
