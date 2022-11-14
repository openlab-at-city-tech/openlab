<?php

// Early versions of PHP choke on namespaces and we can't even conditionally declare them in the main plugin file.
// Include this file as a function call for markdown();
require_once( GravityPerks::get_base_path() . '/vendor/autoload.php' );

$Parsedown = new Parsedown();

return $Parsedown->text( $string );
