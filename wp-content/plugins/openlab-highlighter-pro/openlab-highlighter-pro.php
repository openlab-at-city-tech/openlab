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

	add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );
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
 * Override core comment form.
 *
 * Fixes issue with 'pages' support.
 *
 * @return void
 */
function render_comment_form() {
	$options = \get_option( 'highlighter_settings' );
	$comments_enabled = $options['comments_enabled'] ? $options['comments_enabled'] : false;

	// @todo check is_signular against CPT options.
	if ( $comments_enabled && \is_singular() ) {

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
}
