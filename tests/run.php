<?php
// quick tests for the parts that dont need db. run: php tests/run.php

require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/Tasks.php';
require __DIR__ . '/../app/Performance.php';

$fails = 0;

function check($label, $expected, $got)
{
    global $fails;
    if ($expected !== $got) {
        $fails++;
        echo "FAIL $label: expected " . var_export($expected, true) . ", got " . var_export($got, true) . "\n";
    }
}

// score
check('4 of 5 done, 3 on time', 78, Performance::calculate(5, 4, 3)['score']);
check('breakdown completion', 48.0, Performance::calculate(5, 4, 3)['completion_points']);
check('breakdown on time', 30.0, Performance::calculate(5, 4, 3)['on_time_points']);
check('no tasks', 0, Performance::calculate(0, 0, 0)['score']);
check('all done on time', 100, Performance::calculate(10, 10, 10)['score']);
check('all done all late', 60, Performance::calculate(10, 10, 0)['score']);
check('none done', 0, Performance::calculate(4, 0, 0)['score']);
check('rounding', 66, Performance::calculate(7, 3, 3)['score']);

// rating edges
check('80', 'Excellent', Performance::ratingFor(80));
check('79', 'Good', Performance::ratingFor(79));
check('60', 'Good', Performance::ratingFor(60));
check('59', 'Average', Performance::ratingFor(59));
check('40', 'Average', Performance::ratingFor(40));
check('39', 'Needs improvement', Performance::ratingFor(39));

// overdue
$yesterday = date('Y-m-d', strtotime('-1 day'));
$tomorrow  = date('Y-m-d', strtotime('+1 day'));

check('late and open', true, Tasks::isOverdue(['status' => 'todo', 'due_date' => $yesterday]));
check('late but completed', false, Tasks::isOverdue(['status' => 'completed', 'due_date' => $yesterday]));
check('due today', false, Tasks::isOverdue(['status' => 'todo', 'due_date' => date('Y-m-d')]));
check('due tomorrow', false, Tasks::isOverdue(['status' => 'todo', 'due_date' => $tomorrow]));
check('no due date', false, Tasks::isOverdue(['status' => 'todo', 'due_date' => null]));

// helpers
check('good date', true, valid_date('2025-03-14'));
check('30 feb', false, valid_date('2025-02-30'));
check('wrong format', false, valid_date('14-03-2025'));
check('esc', '&lt;b&gt;', esc('<b>'));

echo $fails ? "$fails failed\n" : "all ok\n";
exit($fails ? 1 : 0);
