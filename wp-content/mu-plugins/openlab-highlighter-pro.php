<?php
/**
 * Plugin Name: OpenLab Highlighter Pro
 * Plugin URI:  https://openlab.citytech.cuny.edu/
 * Description: Modifications and hotfixes for Highlighter Pro plugin.
 * Author:      OpenLab
 * Author URI:  https://openlab.citytech.cuny.edu/
 * Version:     1.0.0
 */

namespace OpenLab\HighlighterPro;

function bootstrap() {
	// Bail, if Hightlighter Pro isn't active.
	if ( ! function_exists( 'highlighter_body_class' ) ) {
		return;
	}

	// Remove core hooks
	remove_action( 'wp_footer', 'highlighter_comment_form' );
	remove_action( 'wp_footer', 'highlighter_login' );

	// Disable AJAX callbacks
	remove_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
	remove_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
	remove_action( 'wp_ajax_nopriv_ajaxforgotpassword', 'ajax_forgotPassword' );

	// Don't allow content sumbission unauthorized users.
	remove_action( 'wp_ajax_nopriv_ajax-update-content', 'ajax_update_content' );
	remove_action( 'wp_ajax_nopriv_ajaxcomments', 'ajax_submit_comment' );

	// Override stats and fix stats.
	remove_action( 'loop_start','highlighter_stats_conditional_title' );
	remove_filter( 'the_content', 'highlighter_stats_content', 10, 2 );
	add_filter( 'the_content', __NAMESPACE__ . '\\render_content_stats' );

	add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );
	add_filter( 'comments_open', __NAMESPACE__ . '\\enable_page_comments', 10, 2 );
	add_filter( 'redux/args/highlighter_settings', __NAMESPACE__ . '\\filter_settings' );

	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );
	add_action( 'wp_footer', __NAMESPACE__ . '\\render_comment_form' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

/**
 * Adds 'single' to single page body class.
 *
 * This fixes incorrect check in JS file.
 *
 * @param array $classes
 * @return array $classes
 */
function body_class( $classes ) {
	if ( is_page() ) {
		$classes[] = 'single';
	}

	return $classes;
}

/**
 * Enable comments for pages when plugin is active.
 *
 * @param bool $open
 * @param string $post_id
 * @return bool
 */
function enable_page_comments( $open, $post_id ) {
	$post = get_post( $post_id );

	if ( 'page' !== $post->post_type ) {
		return $open;
	}

	return true;
}

/**
 * Removes unnecessary plugin settings.
 *
 * @param array $args
 * @return array
 */
function filter_settings( $args ) {
	// Disable Customizer panels
	$args['customizer'] = false;

	return $args;
}

/**
 * Override core comment form.
 *
 * Fixes issue with 'pages' support.
 *
 * @return void
 */
function render_comment_form() {
	$options = \get_option( 'highlighter_settings' );
	$types = ! empty( $options['highlighter_enable'] ) ? $options['highlighter_enable'] : [];
	$comments_enabled = $options['comments_enabled'] ? $options['comments_enabled'] : false;

	if ( ! $comments_enabled && ! \is_singular( $types ) ) {
		return;
	}

	// get some user info
	$current_user = \wp_get_current_user();
	$avatar = '';
	$name = '';

	if ( $current_user instanceof \WP_User ) {
		$avatar = \get_avatar( $current_user->user_email, 32 );
		$name = $current_user->display_name;
	}

	// setup comment form
	$comment_args = array(
		'id_form' => 'highlighter-comment-form',
		'title_reply' => '',
		'id_submit'   => 'highlighter-comment-submit',
		'label_submit' => __( 'Respond', 'highlighter' ),
		'submit_button' => '<div class="btn-confirm confirm-yes">
			<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />
		</div>',
		'fields' => apply_filters( 'comment_form_default_fields', array(
				'author' => '',
				'email'  => '',
				'url'    => '' ) ),
		'must_log_in' => '',
		'logged_in_as' => '',
		'comment_field' => '<textarea id="highlighter-comment-textarea" name="comment" aria-required="true"></textarea>',
		'comment_notes_before' => '',
		'comment_notes_after' => '',
	);

	?>

	<div class="highlighter-docked-panel highlighter-comments-wrapper">
		<div class="highlighter-docked-header"><?php _e( 'Add Comment', 'highlighter' ); ?></div>

		<div class="highlighter-comments-user">
			<?php echo $avatar; ?>
			<span class="highlighter-comments-name"><?php echo $name; ?></span>
		</div>

		<div class="highlighter-comment">
			<div class="highlighter-view-loading"><?php _e( 'Loading...', 'highlighter' ); ?></div>

			<?php \comment_form( $comment_args ); ?>

			<div class="btn-confirm confirm-no"><?php _e('Cancel', 'highlighter'); ?></div>
		</div>
	</div>

	<?php
}

/**
 * Override content stats display.
 *
 * @param string $content
 * @return string $content
 */
function render_content_stats( $content ) {
	$options   = \get_option( 'highlighter_settings' );
	$types     = ! empty( $options['highlighter_enable'] ) ? $options['highlighter_enable'] : [];
	$placement = $options['stats_placement'];

	if ( ! \is_singular( $types ) ) {
		return $content;
	}

	if ( $placement === 'before_content' ) {
		$content = \do_shortcode( '[highlighter-stats]' ) . $content;
		return $content;
	}

	if ( $placement === 'after_content' ) {
		$content = $content . \do_shortcode( '[highlighter-stats]' );
		return $content;
	}

	return $content;
}

/**
 * Load assets for Highlighter Pro.
 *
 * @return void
 */
function enqueue_assets() {
	$options = \get_option( 'highlighter_settings' );
	$types   = ! empty( $options['highlighter_enable'] ) ? $options['highlighter_enable'] : [];

	if ( ! \is_singular( $types ) ) {
		return;
	}

	\wp_enqueue_style(
		'openlab-highligter-pro',
		\content_url( 'mu-plugins/css/highlighter-pro.css' ),
		[],
		'1.0.0'
	);
}
