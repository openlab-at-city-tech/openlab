<?php
/**
 * Admin code.
 *
 * @package cac-creative-commons
 */

// Load required functions only on certain pages.
foreach ( array( 'options-writing.php', 'post.php', 'post-new.php' ) as $p ) {
	add_action( "load-{$p}", function() use ( $p ) {
		require_once __DIR__ . '/functions.php';
		require_once __DIR__ . '/admin-functions.php';

		// Page-specific code.
		// 'post-new.php' uses the same code as 'post.php'.
		$page = 'post-new.php' === $p ? 'post.php' : $p;
		require __DIR__ . "/admin-{$page}";
	} );
}

// Save routine for "Settings > Writing" page.
add_action( 'load-options.php', function() {
	require __DIR__ . '/admin-save-options.php';
} );
