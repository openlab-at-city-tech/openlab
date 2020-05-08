<?php
/**
 * Dashboard functionality.
 */

namespace OpenLab\PrivateComments\Admin;

/**
 * Insert a value or key/value pair after a specific key in an array. If key doesn't exist, value is appended
 * to the end of the array.
 *
 * @see https://gist.github.com/costidima/9ef810722d7f83122d947f0aa0c59c3b
 *
 * @param array $array
 * @param string $key
 * @param array $new
 *
 * @return array
 */
function array_insert_after( array $array, $key, array $new ) {
	$keys = array_keys( $array );
	$index = array_search( $key, $keys );
	$pos = false === $index ? count( $array ) : $index + 1;
	return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
}

 /**
  * Display custom comment row action.
  *
  * @param array $actions
  * @param \WP_Comment $comment_id
  * @return array $actions
  */
function comment_row_actions( $actions, $comment ) {
	$comment_id = (int) $comment->comment_ID;
	$is_private = get_comment_meta( $comment_id, 'ol_is_private', true );

	$label  = ! empty( $is_private ) ? __( 'Make Public', 'openlab-private-comments' ) : __( 'Make Private', 'openlab-private-comments' );
	$action = [
		'ol-private-comment' => sprintf(
			'<button data-comment-id="%1$d" data-is-private="%2$d" class="button-link">%3$s</button>',
			$comment_id,
			(int) $is_private,
			$label,
		)
	];

	$actions = array_insert_after( $actions, 'edit', $action );

	return $actions;
}
add_filter( 'comment_row_actions', __NAMESPACE__ . '\\comment_row_actions', 10, 2 );

/**
 * Enqueue custom script.
 *
 * @param string $hook_suffix The current admin page.
 * @return void
 */
function enqueue_assets( $hook_suffix ) {
	// Only load on Comments admin page and dashboard (for "Recent Comments").
	if ( ! in_array( $hook_suffix, [ 'index.php', 'edit-comments.php' ] ) ) {
		return;
	}

	wp_enqueue_script(
		'openlab-private-comments',
		plugins_url( 'assets/js/private-comments.js' , __DIR__ ),
		[ 'jquery' ],
		'1.0.0',
		true
	);

	wp_localize_script( 'openlab-private-comments', 'olPrivateComments', [
		'nonce' => wp_create_nonce( 'ol_private_comments_nonce' ),
	 ] );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );

/**
 * Handle ajax request for comments admin actions.
 *
 * @return void
 */
function handle_ajax_request() {
	check_ajax_referer( 'ol_private_comments_nonce' );

	if ( empty( $_POST['id'] ) || ! isset( $_POST['is_private'] ) ) {
		wp_send_json_error();
	}

	// Set new status, by reversing current one.
	$is_private = empty( $_POST['is_private'] ) ? 1 : 0;
	$comment_id = absint( $_POST['id'] );
	$label      = $is_private ? esc_html__( 'Make Public', 'openlab-private-comments' ) : esc_html__( 'Make Private', 'openlab-private-comments' );

	update_comment_meta( $comment_id, 'ol_is_private', $is_private );

	wp_send_json_success( [
		'is_private' => $is_private,
		'label'      => $label,
	] );
}
add_action( 'wp_ajax_openlab_private_comments', __NAMESPACE__ . '\\handle_ajax_request' );
