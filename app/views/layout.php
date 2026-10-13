<?php $user = Auth::user(); $flash = pull_flash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'PharmaDEM') ?> | PharmaDEM</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php if ($user): ?>
    <header class="topbar">
        <div class="brand">PharmaDEM</div>
        <nav>
            <?php if ($user['role'] === 'manager'): ?>
                <a href="index.php?r=manager/dashboard">Dashboard</a>
                <a href="index.php?r=manager/tasks">Tasks</a>
                <a href="index.php?r=manager/task/new">Assign Task</a>
                <a href="index.php?r=manager/reports">Reports</a>
            <?php else: ?>
                <a href="index.php?r=employee/dashboard">My Tasks</a>
                <a href="index.php?r=employee/updates">Daily Update</a>
            <?php endif; ?>
        </nav>
        <div class="user">
            <?= esc($user['name']) ?> (<?= esc($user['role']) ?>)
            <a class="logout" href="index.php?r=logout">Logout</a>
        </div>
    </header>
<?php endif; ?>

<main class="container">
    <?php if ($flash): ?>
        <div class="flash flash-<?= esc($flash['type']) ?>"><?= esc($flash['message']) ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>
</body>
</html>
