<?php
/**
 * Setups our metabox field for the post edit screen
 *
 * @copyright   Copyright (c) 2014, Andy von Dohren
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render the metabox options in the main Publish box
 *
 * @access public
 * @since 1.0
 * @return void
 */
function pw_esp_add_expiration_field() {

	global $post;

	if( ! empty( $post->ID ) ) {
		$expires = get_post_meta( $post->ID, 'pw_esp_expiration', true );
	}

	$label = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : __( 'never', 'pw-esp' );
	$date  = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : '';
?>
	<div id="pw-esp-expiration-wrap" class="misc-pub-section">
		<span>
			<span class="wp-media-buttons-icon dashicons dashicons-calendar"></span>&nbsp;
			<?php _e( 'Sticky Expires:', 'pw-esp' ); ?>
			<b id="pw-esp-expiration-label"><?php echo $label; ?></b>
		</span>
		<a href="#" id="pw-esp-edit-expiration" class="pw-esp-edit-expiration hide-if-no-js">
			<span aria-hidden="true"><?php _e( 'Edit', 'pw-esp' ); ?></span>&nbsp;
			<span class="screen-reader-text"><?php _e( 'Edit date and time', 'pw-esp' ); ?></span>
		</a>
		<div id="pw-esp-expiration-field" class="hide-if-js">
			<p>
				<input type="text" name="pw-esp-expiration" id="pw-esp-expiration" value="<?php echo esc_attr( $date ); ?>" placeholder="yyyy-mm-dd"/>
			</p>
			<p>
				<a href="#" class="pw-esp-hide-expiration button secondary"><?php _e( 'OK', 'pw-esp' ); ?></a>
				<a href="#" class="pw-esp-hide-expiration cancel"><?php _e( 'Cancel', 'pw-esp' ); ?></a>
			</p>
		</div>
		<?php wp_nonce_field( 'pw_esp_edit_expiration', 'pw_esp_expiration_nonce' ); ?>
	</div>
<?php
}
add_action( 'post_submitbox_misc_actions', 'pw_esp_add_expiration_field' );

/**
 * Save the posts's expiration date
 *
 * @access public
 * @since 1.0
 * @return void
 */
function pw_esp_save_expiration( $post_id = 0 ) {

	if( empty( $_POST['pw_esp_expiration_nonce'] ) ) {
		return;
	}

	if( ! wp_verify_nonce( $_POST['pw_esp_expiration_nonce'], 'pw_esp_edit_expiration' ) ) {
		return;
	}

	if( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return;
	}

	$expiration = ! empty( $_POST['pw-esp-expiration'] ) ? sanitize_text_field( $_POST['pw-esp-expiration'] ) : false;

	if( $expiration ) {

		update_post_meta( $post_id, 'pw_esp_expiration', $expiration );

	} else {

		delete_post_meta( $post_id, 'pw_esp_expiration' );

	}

}
add_action( 'save_post', 'pw_esp_save_expiration' );

/**
 * Load our JS and CSS files
 *
 * @access public
 * @since 1.0
 * @return void
 */
function pw_esp_scripts() {
	wp_enqueue_style( 'jquery-ui-css', PW_ESP_ASSETS_URL . '/css/jquery-ui-fresh.min.css' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'pw-esp-expiration', PW_ESP_ASSETS_URL . '/js/edit.js' );
}
add_action( 'load-post-new.php', 'pw_esp_scripts' );
add_action( 'load-post.php', 'pw_esp_scripts' );
