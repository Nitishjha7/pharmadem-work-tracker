<?php

function esc(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $route): void
{
    header('Location: index.php?r=' . $route);
    exit;
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function valid_date(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d !== false && $d->format('Y-m-d') === $date;
}

function rating_css(string $rating): string
{
    return 'rating-' . str_replace(' ', '-', strtolower($rating));
}

// Renders app/views/<name>.php and wraps it in the layout.
function render(string $name, array $data = []): void
{
    extract($data);

    ob_start();
    require __DIR__ . "/views/$name.php";
    $content = ob_get_clean();

    require __DIR__ . '/views/layout.php';
}
