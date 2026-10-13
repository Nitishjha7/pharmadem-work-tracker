<h1>Daily Update</h1>
<p class="muted">Write what you did today. You can come back and edit it until the day ends.</p>

<form method="post" action="index.php?r=daily/save" class="form-card">
    <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">

    <label>What did you work on today? <span class="req">*</span></label>
    <textarea name="work_summary" rows="4" required><?= e($current['work_summary'] ?? '') ?></textarea>

    <label>Blockers (if any)</label>
    <textarea name="blockers" rows="2"><?= e($current['blockers'] ?? '') ?></textarea>

    <button type="submit"><?= $current ? 'Update' : 'Save' ?> entry for <?= e($today) ?></button>
</form>

<h2 class="section-head">Past updates</h2>

<?php if (!$updates): ?>
    <p class="muted">Nothing posted yet.</p>
<?php endif; ?>

<div class="update-list">
    <?php foreach ($updates as $u): ?>
        <article class="update-item">
            <div class="update-date"><?= e($u['update_date']) ?></div>
            <div class="update-body">
                <p><?= nl2br(e($u['work_summary'])) ?></p>
                <?php if ($u['blockers']): ?>
                    <p class="blocker"><strong>Blockers:</strong> <?= nl2br(e($u['blockers'])) ?></p>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</div>
