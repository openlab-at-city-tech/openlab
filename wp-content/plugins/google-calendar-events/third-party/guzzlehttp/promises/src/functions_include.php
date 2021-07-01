<?php

namespace SimpleCalendar\plugin_deps;

// Don't redefine the functions if included multiple times.
if (!\function_exists('SimpleCalendar\\plugin_deps\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
