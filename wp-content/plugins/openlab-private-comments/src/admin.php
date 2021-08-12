<?php
/**
 * Dashboard functionality.
 */

namespace OpenLab\PrivateComments\Admin;

use const OpenLab\PrivateComments\VERSION;
use const OpenLab\PrivateComments\PLUGIN_FILE;

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
	$user_id           = (int) $comment->user_id;
	$comment_id        = (int) $comment->comment_ID;
	$is_parent_private = (bool) get_comment_meta( $comment->comment_parent, 'ol_is_private', true );
	$is_private        = (bool) get_comment_meta( $comment_id, 'ol_is_private', true );

	// When parent comment is private, disable toggle action.
	if ( $is_parent_private && $is_private ) {
		return $actions;
	}

	// Admins can only make their comments public.
	if ( $is_private && get_current_user_id() !== $user_id ) {
		return $actions;
	}

	$label  = $is_private ? __( 'Make Public', 'openlab-private-comments' ) : __( 'Make Private', 'openlab-private-comments' );
	$action = [
		'ol-private-comment' => sprintf(
			'<button data-comment-id="%1$d" data-is-private="%2$d" class="button-link">%3$s</button>',
			$comment_id,
			(int) $is_private,
			$label
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
		'ol-private-comments-admin',
		plugins_url( 'assets/js/private-comments-admin.js' , __DIR__ ),
		[ 'jquery' ],
		VERSION,
		true
	);

	wp_localize_script( 'ol-private-comments-admin', 'olPrivateComments', [
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

/**
 * Show activation admin notice.
 *
 * @return void
 */
function admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! apply_filters( 'olpc_display_notices', true ) ) {
		return;
	}

	// Notice was dissmised.
	if ( get_option( 'olpc_notice_dismissed' ) ) {
		return;
	}

	$dismiss_url = $_SERVER['REQUEST_URI'];
	$nonce       = wp_create_nonce( 'olpc_notice_dismiss' );
	$dismiss_url = add_query_arg( 'olpc-notice-dismiss', '1', $dismiss_url );
	$dismiss_url = add_query_arg( '_wpnonce', $nonce, $dismiss_url );

	?>
	<style type="text/css">
		.olpc-notice-message p {
			display: flex;
		}
		.olpc-notice-message-dismiss {
			align-self: center;
			margin-left: 8px;
		}
	</style>
	<div class="notice notice-warning fade olpc-notice-message">
		<p><span><?php esc_html_e( 'Please note: If you deactivate the OpenLab Private Comments plugin, any private comments made while the plugin was activated will become visible to anyone who can view your site.', 'openlab-private-comments' ); ?></span>
		<a class="olpc-notice-message-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss', 'openlab-private-comments' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', __NAMESPACE__ . '\\admin_notice' );

/**
 * Catch notice dismissal.
 *
 * @return void
 */
function catch_notice_dismissal() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['olpc-notice-dismiss'] ) ) {
		return;
	}

	check_admin_referer( 'olpc_notice_dismiss' );

	update_option( 'olpc_notice_dismissed', 1 );
}
add_action( 'admin_init', __NAMESPACE__ . '\\catch_notice_dismissal' );

/**
 * Display confirmation modal on the plugin deactivation.
 *
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @return void
 */
function deactivation_notice( $plugin_file ) {
	if ( 'openlab-private-comments/openlab-private-comments.php' !== $plugin_file ) {
		return;
	}

	if ( ! apply_filters( 'olpc_display_notices', true ) ) {
		return;
	}

	wp_enqueue_script( 'olpc-deactivation', plugins_url( 'assets/js/deactivation.js', PLUGIN_FILE ), [], VERSION, true );
	wp_localize_script( 'olpc-deactivation', 'OLPCDeactivate', [
		'message' => esc_html__( 'Please note: If you deactivate the OpenLab Private Comments plugin, any private comments made while the plugin was activated will become visible to anyone who can view your site.', 'openlab-private-comments' ),
	] );
}
add_action( 'after_plugin_row', __NAMESPACE__ . '\\deactivation_notice' );
