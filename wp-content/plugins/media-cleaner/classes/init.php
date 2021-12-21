<?php

if ( class_exists( 'MeowPro_WPMC_Core' ) && class_exists( 'Meow_WPMC_Core' ) ) {
	function wpmc_thanks_admin_notices() {
		echo '<div class="error"><p>' . __( 'Thanks for installing the Pro version of Media Cleaner :) However, the free version is still enabled. Please disable or uninstall it.', 'media-cleaner' ) . '</p></div>';
	}
	add_action( 'admin_notices', 'wpmc_thanks_admin_notices' );
	return;
}

spl_autoload_register(function ( $class ) {
  $necessary = true;
  $file = null;
  if ( strpos( $class, 'Meow_WPMC' ) !== false ) {
    $file = WPMC_PATH . '/classes/' . str_replace( 'meow_wpmc_', '', strtolower( $class ) ) . '.php';
  }
  else if ( strpos( $class, 'MeowCommon_' ) !== false ) {
    $file = WPMC_PATH . '/common/' . str_replace( 'meowcommon_', '', strtolower( $class ) ) . '.php';
  }
  else if ( strpos( $class, 'MeowCommonPro_' ) !== false ) {
    $necessary = false;
    $file = WPMC_PATH . '/common/premium/' . str_replace( 'meowcommonpro_', '', strtolower( $class ) ) . '.php';
  }
  else if ( strpos( $class, 'MeowPro_WPMC' ) !== false ) {
    $necessary = false;
    $file = WPMC_PATH . '/premium/' . str_replace( 'meowpro_wpmc_', '', strtolower( $class ) ) . '.php';
  }
  if ( $file ) {
    if ( !$necessary && !file_exists( $file ) ) {
      return;
    }
    require( $file );
  }
});

// In admin or Rest API request (REQUEST URI begins with '/wp-json/')
if ( is_admin() || MeowCommon_Helpers::is_rest() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	global $mfrh_core;
	$mfrh_core = new Meow_WPMC_Core();
}

?>