<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

require_once( 'classes/core.php' );
wpmc_uninstall();
