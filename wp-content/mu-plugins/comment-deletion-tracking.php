<?php

function cac_log_comment_deletion( $comment_id, $comment ) {
	$log_path = WP_CONTENT_DIR . '/uploads/comment-deletion.log';

	$message = sprintf(
		'Deleting comment %s on site %s %s',
		$comment_id,
		get_current_blog_id(),
		get_bloginfo( 'url' )
	);

	$message .= "\n\n";
	$message .= sprintf( 'Current user: %s', get_current_user_id() );
	$message .= "\n\n";

	$message .= print_r( $_SERVER, true );
	$message .= "\n\n";
	$message .= wp_debug_backtrace_summary();

	$message .= "\n\n\n\n";

	error_log( date( '[Y-m-d H:i:s]' ) . ' ' . $message, 3, $log_path );
}
add_action( 'delete_comment', 'cac_Log_comment_deletion', 10, 2 );
add_action( 'spam_comment', 'cac_Log_comment_deletion', 10, 2 );
