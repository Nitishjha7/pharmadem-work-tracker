<?php

session_start();

$root = dirname(__DIR__);
date_default_timezone_set((require "$root/config.php")['timezone']);

foreach (['Database', 'helpers', 'Auth', 'Users', 'Tasks', 'DailyUpdates', 'Performance',
          'AuthController', 'EmployeeController', 'ManagerController'] as $file) {
    require "$root/app/$file.php";
}

// route => [http method, controller, function]
$routes = [
    'login'               => ['GET',  'AuthController', 'showLogin'],
    'login.submit'        => ['POST', 'AuthController', 'login'],
    'logout'              => ['GET',  'AuthController', 'logout'],
    'dashboard'           => ['GET',  'AuthController', 'dashboard'],

    'employee/dashboard'  => ['GET',  'EmployeeController', 'dashboard'],
    'employee/updates'    => ['GET',  'EmployeeController', 'updates'],
    'task/status'         => ['POST', 'EmployeeController', 'updateStatus'],
    'daily/save'          => ['POST', 'EmployeeController', 'saveUpdate'],

    'manager/dashboard'   => ['GET',  'ManagerController', 'dashboard'],
    'manager/tasks'       => ['GET',  'ManagerController', 'tasks'],
    'manager/task/new'    => ['GET',  'ManagerController', 'newTask'],
    'manager/task/create' => ['POST', 'ManagerController', 'createTask'],
    'manager/task/edit'   => ['GET',  'ManagerController', 'editTask'],
    'manager/task/update' => ['POST', 'ManagerController', 'updateTask'],
    'manager/task/delete' => ['POST', 'ManagerController', 'deleteTask'],
    'manager/reports'     => ['GET',  'ManagerController', 'reports'],
];

$route = $_GET['r'] ?? 'login';

if (!isset($routes[$route])) {
    http_response_code(404);
    exit('Page not found.');
}

[$httpMethod, $class, $action] = $routes[$route];

if ($_SERVER['REQUEST_METHOD'] !== $httpMethod) {
    http_response_code(405);
    exit('Method not allowed.');
}

try {
    $class::$action();
} catch (PDOException $e) {
    error_log('DB error: ' . $e->getMessage());
    http_response_code(500);
    exit('Database error. Did you import sql/schema.sql and set the DB details in config.php?');
}
