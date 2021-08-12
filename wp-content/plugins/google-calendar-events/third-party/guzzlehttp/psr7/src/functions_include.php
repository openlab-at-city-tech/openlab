<?php

namespace SimpleCalendar\plugin_deps;

// Don't redefine the functions if included multiple times.
if (!\function_exists('SimpleCalendar\\plugin_deps\\GuzzleHttp\\Psr7\\str')) {
    require __DIR__ . '/functions.php';
}
