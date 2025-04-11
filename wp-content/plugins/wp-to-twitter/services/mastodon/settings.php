<?php
/**
 * Connect Mastodon for XPoster
 *
 * @category Mastodon
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.xposterpro.com
 */

/**
 * Update Mastodon settings.
 *
 * @param int|boolean   $auth Author.
 * @param array|boolean $post POST data.
 */
function wpt_update_mastodon_settings( $auth = false, $post = false ) {
	if ( isset( $post['mastodon_settings'] ) ) {
		switch ( $post['mastodon_settings'] ) {
			case 'wtt_oauth_test':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}

				if ( ! empty( $post['wpt_mastodon_token'] ) && ! empty( $post['wpt_mastodon_instance'] ) ) {
					$ack = sanitize_text_field( trim( $post['wpt_mastodon_token'] ) );
					$acs = sanitize_text_field( trim( untrailingslashit( $post['wpt_mastodon_instance'] ) ) );

					if ( ! $auth ) {
						// If values are filled with asterisks, do not update; these are masked values.
						if ( stripos( $ack, '***' ) === false ) {
							update_option( 'wpt_mastodon_token', $ack );
						}
						if ( stripos( $acs, '***' ) === false ) {
							update_option( 'wpt_mastodon_instance', $acs );
						}
					} else {
						if ( stripos( $ack, '***' ) === false ) {
							update_user_meta( $auth, 'wpt_mastodon_token', $ack );
						}
						if ( stripos( $acs, '***' ) === false ) {
							update_user_meta( $auth, 'wpt_mastodon_instance', $acs );
						}
					}
					$message  = 'failed';
					$validate = array(
						'token'    => $ack,
						'instance' => $acs,
					);
					$verify   = wpt_mastodon_connection( $auth, $validate );
					if ( isset( $verify['username'] ) ) {
						$username = sanitize_text_field( stripslashes( $verify['username'] ) );
						if ( ! $auth ) {
							update_option( 'wpt_mastodon_username', $username );
						} else {
							update_user_meta( $auth, 'wpt_mastodon_username', $username );
						}
						$message = 'success';
						delete_option( 'wpt_curl_error' );
						if ( '1' === get_option( 'wp_debug_oauth' ) ) {
							echo '<br /><strong>Account Verification Data:</strong><br />';
							wpt_format_error( $verify );
							echo '</pre>';
						}
					} else {
						$message = 'noconnection';
					}
				} else {
					$message = 'nodata';
				}

				return $message;
				break;
			case 'wtt_mastodon_update':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				$option  = get_option( 'wpt_disabled_services', array() );
				$disable = isset( $post['wpt_disabled_services'] ) ? true : false;
				if ( $disable ) {
					$option['mastodon'] = 'true';
				} else {
					unset( $option['mastodon'] );
				}
				if ( isset( $_POST['wpt_mastodon_length'] ) ) {
					update_option( 'wpt_mastodon_length', intval( $_POST['wpt_mastodon_length'] ) );
				}
				update_option( 'wpt_disabled_services', $option );
				break;
			case 'wtt_mastodon_disconnect':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				if ( ! $auth ) {
					update_option( 'wpt_mastodon_token', '' );
					update_option( 'wpt_mastodon_instance', '' );
					update_option( 'wpt_mastodon_username', '' );
				} else {
					delete_user_meta( $auth, 'wpt_mastodon_token' );
					delete_user_meta( $auth, 'wpt_mastodon_instance' );
					delete_user_meta( $auth, 'wpt_mastodon_username' );
				}
				$message = 'cleared';

				return $message;
				break;
		}
	}

	return '';
}

/**
 * Connect or disconnect from Mastodon API form.
 *
 * @param mixed int/boolean $auth Current author.
 */
function wtt_connect_mastodon( $auth = false ) {
	if ( ! $auth ) {
		echo '<div class="ui-sortable meta-box-sortables">';
		echo '<div class="postbox">';
	}
	if ( $auth ) {
		wpt_update_authenticated_users();
	}

	$class   = ( $auth ) ? 'wpt-profile' : 'wpt-settings';
	$connect = wpt_mastodon_connection( $auth );
	if ( ! $connect ) {
		$ack = ( ! $auth ) ? get_option( 'wpt_mastodon_token' ) : get_user_meta( $auth, 'wpt_mastodon_token', true );
		$acs = ( ! $auth ) ? get_option( 'wpt_mastodon_instance' ) : get_user_meta( $auth, 'wpt_mastodon_instance', true );
		?>
		<h3 class="wpt-has-link"><span><?php esc_html_e( 'Connect to Mastodon', 'wp-to-twitter' ); ?></span> <a href="https://xposterpro.com/connecting-xposter-and-mastodon/" class="button button-secondary"><?php esc_html_e( 'Instructions', 'wp-to-twitter' ); ?></a></h3>
		<div class="inside <?php esc_attr( $class ); ?>">
		<?php
		echo ( ! $auth ) ? '<form action="" method="post" class="wpt-connection-form">' : '';
		?>
			<ol class="wpt-oauth-settings">
				<li><?php echo esc_html_e( 'Navigate to Preferences > Settings > Development in your Mastodon account.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Click on "New application".', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Name your application.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Add your website URL', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Set the API Scopes for your application. Required: <code>read</code>, <code>write:statuses</code> and <code>write:media</code>.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Submit your application.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Select your application from the list of "Your Applications."', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Copy your Access Token', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Add your Mastodon server URL', 'wp-to-twitter' ); ?></li>
				<div class="tokens auth-fields">
				<p>
					<label for="wpt_mastodon_token"><?php echo esc_html_e( 'Access Token', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wpt_mastodon_token" id="wpt_mastodon_token" value="<?php echo esc_attr( wpt_mask_attr( $ack ) ); ?>" />
				</p>
				<p>
					<label for="wpt_mastodon_instance"><?php echo esc_html_e( 'Instance URL', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wpt_mastodon_instance" id="wpt_mastodon_instance" placeholder="https://toot.io" value="<?php echo esc_attr( $acs ); ?>" />
				</p>
				</div>
			</ol>
			<?php
			echo ( ! $auth ) ? '<p class="submit"><input type="submit" name="submit" class="button-primary" value="' . esc_attr__( 'Connect to Mastodon', 'wp-to-twitter' ) . '" /></p>' : '';
			?>
			<input type="hidden" name="mastodon_settings" value="wtt_oauth_test" class="hidden" />
			<?php
			if ( ! $auth ) {
				wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true );
				echo '</form>';
			}
			?>
		</div>
		<?php
	} elseif ( $connect ) {
		$ack   = ( ! $auth ) ? get_option( 'wpt_mastodon_token' ) : get_user_meta( $auth, 'wpt_mastodon_token', true );
		$acs   = ( ! $auth ) ? get_option( 'wpt_mastodon_instance' ) : get_user_meta( $auth, 'wpt_mastodon_instance', true );
		$uname = ( ! $auth ) ? get_option( 'wpt_mastodon_username' ) : get_user_meta( $auth, 'wpt_mastodon_username', true );
		$site  = get_bloginfo( 'name' );
		?>
		<h3><?php esc_html_e( 'Mastodon Connection', 'wp-to-twitter' ); ?></h3>
		<div class="inside <?php echo esc_attr( $class ); ?>">
		<?php
		echo ( ! $auth ) ? '<form action="" method="post" class="wpt-connection-form">' : '';
		?>
			<div id="wtt_authentication_display">
				<ul>
					<li><strong class="auth_label"><?php echo esc_html_e( 'Username ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><a href="<?php echo esc_url( $acs ); ?>/@<?php echo esc_attr( $uname ); ?>"><?php echo esc_attr( $uname ); ?></a></code></li>
					<li><strong class="auth_label"><?php echo esc_html_e( 'Access Token ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $ack ) ); ?></code></li>
					<li><strong class="auth_label"><?php echo esc_html_e( 'Mastodon Instance', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( $acs ); ?></code></li>
				</ul>
				<div>
			<?php
			if ( ! $auth ) {
				// Translators: Name of the current site.
				$text = sprintf( __( 'Disconnect %s from Mastodon', 'wp-to-twitter' ), $site );
				?>
				<input type="submit" name="submit" class="button-primary" value="<?php echo esc_attr( $text ); ?>" />
				<input type="hidden" name="mastodon_settings" value="wtt_mastodon_disconnect" class="hidden" />
				<?php
				wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true );
				echo '</form>';
			} else {
				?>
				<input type="checkbox" name="mastodon_settings" value="wtt_mastodon_disconnect" id="disconnect_mastodon" /> <label for="disconnect_mastodon"><?php esc_html_e( 'Disconnect Your Account from Mastodon', 'wp-to-twitter' ); ?></label>
				<?php
			}
			?>
				</div>
			</div>
			<?php
			if ( ! $auth ) {
				$disabled = get_option( 'wpt_disabled_services', array() );
				$checked  = ( in_array( 'mastodon', array_keys( $disabled ), true ) ) ? 'checked' : '';
				?>
				<form action="" method="post" class="wpt-connection-form">
					<?php wpt_service_length( 'mastodon' ); ?>
					<p class="checkboxes"><input <?php checked( 'checked', $checked ); ?> type="checkbox" name="wpt_disabled_services[]" id="wpt_disable_mastodon" value="mastodon"><label for="wpt_disable_mastodon"><?php esc_html_e( 'Disable Posting to Mastodon', 'wp-to-twitter' ); ?></label></p>
					<input type="hidden" name="mastodon_settings" value="wtt_mastodon_update"><input type="submit" name="wtt_mastodon_update" class="button-secondary" value="<?php esc_html_e( 'Save Changes', 'wp-to-twitter' ); ?>" />
					<?php wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true ); ?>
				</form>
				<?php
			}
			?>
		</div>
		<?php
	}
	if ( ! $auth ) {
		?>
	</div>
</div>
		<?php
	}
}
