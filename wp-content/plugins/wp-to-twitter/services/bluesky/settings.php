<?php
/**
 * Connect Bluesky for XPoster
 *
 * @category Bluesky
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2
 * @link     https://www.xposterpro.com
 */

/**
 * Normalize a Bluesky identifier entered in settings.
 *
 * Accepts handle, DID, @handle, or bsky.app profile URL, and converts to the
 * identifier format expected by createSession.
 *
 * @param string $identifier Raw identifier.
 *
 * @return string Normalized identifier.
 */
function wpt_normalize_bluesky_identifier( $identifier ) {
	$identifier = sanitize_text_field( trim( (string) $identifier ) );
	if ( '' === $identifier ) {
		return '';
	}

	$parts = wp_parse_url( $identifier );
	if ( is_array( $parts ) && isset( $parts['host'] ) && isset( $parts['path'] ) ) {
		$path = trim( $parts['path'], '/' );
		if ( str_contains( $path, 'profile/' ) ) {
			$segments   = explode( '/', $path );
			$identifier = end( $segments );
		}
	}

	$identifier = ltrim( trim( $identifier ), '@' );

	if ( 0 === stripos( $identifier, 'did:' ) ) {
		return strtolower( $identifier );
	}

	$identifier = strtolower( $identifier );

	// Handles allow letters, numbers, dots and hyphens.
	return preg_replace( '/[^a-z0-9.-]/', '', $identifier );
}

/**
 * Update Bluesky settings.
 *
 * @param int|boolean   $auth Author.
 * @param array|boolean $post POST data.
 */
function wpt_update_bluesky_settings( $auth = false, $post = false ) {
	if ( isset( $post['bluesky_settings'] ) ) {
		switch ( $post['bluesky_settings'] ) {
			case 'wtt_oauth_test':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}

				$ack         = isset( $post['wpt_bluesky_token'] ) ? sanitize_text_field( trim( $post['wpt_bluesky_token'] ) ) : '';
				$user        = isset( $post['wpt_bluesky_username'] ) ? wpt_normalize_bluesky_identifier( $post['wpt_bluesky_username'] ) : '';
				$is_masked   = ( false !== stripos( $ack, '***' ) );
				$stored_pass = ( ! $auth ) ? get_option( 'wpt_bluesky_token', '' ) : get_user_meta( $auth, 'wpt_bluesky_token', true );

				if ( ! $auth ) {
					// Always allow username updates, even if token is masked.
					update_option( 'wpt_bluesky_username', $user );
					if ( '' !== $ack && ! $is_masked ) {
						update_option( 'wpt_bluesky_token', $ack );
						$stored_pass = $ack;
					}
				} else {
					update_user_meta( $auth, 'wpt_bluesky_username', $user );
					if ( '' !== $ack && ! $is_masked ) {
						update_user_meta( $auth, 'wpt_bluesky_token', $ack );
						$stored_pass = $ack;
					}
				}

				if ( '' !== $stored_pass && '' !== $user ) {
					$message  = 'failed';
					$validate = array(
						'password'   => $stored_pass,
						'identifier' => $user,
					);
					$verify   = wpt_bluesky_connection( $auth, $validate );
					if ( '1' === get_option( 'wp_debug_oauth' ) ) {
						echo '<br /><strong>Account Verification Data:</strong><br /><pre>';
						wpt_format_error( $verify );
						echo '</pre>';
					}
					if ( isset( $verify['active'] ) && $verify['active'] ) {
						$message = 'success';
						delete_option( 'wpt_curl_error' );

					} else {
						$message = 'noconnection';
					}
				} else {
					$message = 'nodata';
				}

				return $message;
				break;
			case 'wtt_bluesky_update':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				$option  = get_option( 'wpt_disabled_services', array() );
				$disable = isset( $post['wpt_disabled_services'] ) ? true : false;
				if ( $disable ) {
					$option['bluesky'] = 'true';
				} else {
					unset( $option['bluesky'] );
				}
				if ( isset( $_POST['wpt_bluesky_length'] ) ) {
					update_option( 'wpt_bluesky_length', intval( $_POST['wpt_bluesky_length'] ) );
				}
				update_option( 'wpt_disabled_services', $option );
				break;
			case 'wtt_bluesky_disconnect':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				if ( ! $auth ) {
					update_option( 'wpt_bluesky_token', '' );
					update_option( 'wpt_bluesky_username', '' );
				} else {
					delete_user_meta( $auth, 'wpt_bluesky_token' );
					delete_user_meta( $auth, 'wpt_bluesky_username' );
				}
				$message = 'cleared';

				return $message;
				break;
		}
	}

	return '';
}

/**
 * Connect or disconnect from Bluesky API form.
 *
 * @param mixed int/boolean $auth Current author.
 */
function wtt_connect_bluesky( $auth = false ) {
	if ( ! $auth ) {
		echo '<div class="ui-sortable meta-box-sortables">';
		echo '<div class="postbox">';
	}
	if ( $auth ) {
		wpt_update_authenticated_users();
	}

	$class   = ( $auth ) ? 'wpt-profile' : 'wpt-settings';
	$connect = wpt_bluesky_connection( $auth );
	if ( ! $connect ) {
		$ack  = ( ! $auth ) ? get_option( 'wpt_bluesky_token' ) : get_user_meta( $auth, 'wpt_bluesky_token', true );
		$user = ( ! $auth ) ? get_option( 'wpt_bluesky_username' ) : get_user_meta( $auth, 'wpt_bluesky_username', true );
		?>
		<h3 class="wpt-has-link"><span><?php esc_html_e( 'Connect to Bluesky', 'wp-to-twitter' ); ?></span> <a href="https://xposterpro.com/connecting-xposter-and-bluesky/" class="button button-secondary"><?php esc_html_e( 'Instructions', 'wp-to-twitter' ); ?></a></h3>
		<div class="inside <?php esc_attr( $class ); ?>">
		<?php
		echo ( ! $auth ) ? '<form action="" method="post" class="wpt-connection-form">' : '';
		?>
			<ol class="wpt-oauth-settings">
				<li><?php echo esc_html_e( 'Navigate to Settings > Privacy and Security > App passwords in your Bluesky account.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Click on "Add App Password".', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Name your app password.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Copy your App Password.', 'wp-to-twitter' ); ?></li>
				<li><?php echo esc_html_e( 'Add your App Password and Bluesky Handle to setings', 'wp-to-twitter' ); ?>
				<div class="tokens auth-fields">
				<p>
					<label for="wpt_bluesky_username"><?php echo esc_html_e( 'Bluesky Handle', 'wp-to-twitter' ); ?></label>
					<input placeholder="joedolson.bsky.social" type="text" size="45" name="wpt_bluesky_username" id="wpt_bluesky_username" value="<?php echo esc_attr( wpt_mask_attr( $user ) ); ?>" />
				</p>
				<p>
					<label for="wpt_bluesky_token"><?php echo esc_html_e( 'App Password', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wpt_bluesky_token" id="wpt_bluesky_token" value="<?php echo esc_attr( wpt_mask_attr( $ack ) ); ?>" />
				</p>
				</div></li>
			</ol>
			<?php
			echo ( ! $auth ) ? '<p class="submit"><input type="submit" name="submit" class="button-primary" value="' . esc_attr__( 'Connect to Bluesky', 'wp-to-twitter' ) . '" /></p>' : '';
			?>
			<input type="hidden" name="bluesky_settings" value="wtt_oauth_test" class="hidden" />
			<?php
			if ( ! $auth ) {
				wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true );
				echo '</form>';
			}
			?>
		</div>
		<?php
	} elseif ( $connect ) {
		$ack   = ( ! $auth ) ? get_option( 'wpt_bluesky_token' ) : get_user_meta( $auth, 'wpt_bluesky_token', true );
		$uname = ( ! $auth ) ? get_option( 'wpt_bluesky_username' ) : get_user_meta( $auth, 'wpt_bluesky_username', true );
		$site  = get_bloginfo( 'name' );
		?>
		<h3><?php esc_html_e( 'Bluesky Connection', 'wp-to-twitter' ); ?></h3>
		<div class="inside <?php echo esc_attr( $class ); ?>">
		<?php
		echo ( ! $auth ) ? '<form action="" method="post" class="wpt-connection-form">' : '';
		?>
			<div id="wtt_authentication_display">
			<ul>
					<li><strong class="auth_label"><?php echo esc_html_e( 'Username ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><a href="https://bsky.app/profile/<?php echo esc_attr( $uname ); ?>"><?php echo esc_attr( $uname ); ?></a></code></li>
					<li><strong class="auth_label"><?php echo esc_html_e( 'Access Token ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $ack ) ); ?></code></li>
				</ul>
				<div>
				<?php
				if ( ! $auth ) {
					// Translators: Name of the current site.
					$text = sprintf( __( 'Disconnect %s from Bluesky', 'wp-to-twitter' ), $site );
					?>
					<input type="submit" name="submit" class="button-primary" value="<?php echo esc_attr( $text ); ?>" />
					<input type="hidden" name="bluesky_settings" value="wtt_bluesky_disconnect" class="hidden" />
					<?php
						wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true );
						echo '</form>';
				} else {
					?>
					<input type="checkbox" name="bluesky_settings" value="wtt_bluesky_disconnect" id="disconnect_bluesky" /> <label for="disconnect_bluesky"><?php esc_html_e( 'Disconnect Your Account from Bluesky', 'wp-to-twitter' ); ?></label>
					<?php
				}
				?>
				</div>
			</div>
			<?php
			if ( ! $auth ) {
				$disabled = get_option( 'wpt_disabled_services', array() );
				$checked  = ( in_array( 'bluesky', array_keys( $disabled ), true ) ) ? 'checked' : '';
				?>
				<form action="" method="post" class="wpt-connection-form">
					<?php wpt_service_length( 'bluesky' ); ?>
					<p class="checkboxes"><input <?php checked( 'checked', $checked ); ?> type="checkbox" name="wpt_disabled_services[]" id="wpt_disable_bluesky" value="bluesky"><label for="wpt_disable_bluesky"><?php esc_html_e( 'Disable Posting to Bluesky', 'wp-to-twitter' ); ?></label></p>
					<input type="hidden" name="bluesky_settings" value="wtt_bluesky_update"><input type="submit" name="wtt_bluesky_update" class="button-secondary" value="<?php esc_html_e( 'Save Changes', 'wp-to-twitter' ); ?>" />
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
