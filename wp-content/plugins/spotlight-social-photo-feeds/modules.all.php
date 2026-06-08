<?php

use RebelCode\Spotlight\Instagram\Plugin;
$modules = (require __DIR__ . '/modules.core.php');
// Filter the modules
$modules = apply_filters( Plugin::FILTER . '/modules', $modules );
return $modules;