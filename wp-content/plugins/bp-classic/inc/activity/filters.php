<?php
/**
 * BP Classic Activity Filters.
 *
 * @package bp-classic\inc\activity
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the BP Default `activity/comment.php` template path
 * for very very old themes needing it.
 *
 * @since 1.0.0
 *
 * @param false $template False to inform the activity template wasn't found.
 * @return string The BP Default `activity/comment.php` template path.
 */
function bp_classic_activity_recurse_comments_template( $template = false ) {
	return buddypress()->old_themes_dir . '/bp-default/activity/comment.php';
}
add_filter( 'bp_activity_recurse_comments_template', 'bp_classic_activity_recurse_comments_template' );
