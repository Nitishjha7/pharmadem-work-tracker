<?php

// DB settings. Environment variables win, otherwise the XAMPP defaults are used.
return [
    // php and mysql must agree on what "today" is, overdue and on-time depend on it
    'timezone' => getenv('APP_TZ') ?: 'Asia/Kolkata',

    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'port'    => getenv('DB_PORT') ?: '3306',
        'name'    => getenv('DB_NAME') ?: 'pharmadem',
        'user'    => getenv('DB_USER') ?: 'root',
        'pass'    => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];
