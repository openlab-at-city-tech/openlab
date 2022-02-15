<?php
/**
 * Connect OAuth for WP to Twitter
 *
 * @category OAuth
 * @package  WP to Twitter
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to test validity of credentials
 *
 * @param mixed int/boolean $auth Current author.
 * @param string            $context Use context.
 *
 * @return Is authenticated.
 */
function wtt_oauth_test( $auth = false, $context = '' ) {
	if ( ! $auth ) {
		return ( wtt_oauth_credentials_to_hash() === get_option( 'wtt_oauth_hash' ) );
	} else {
		$return = ( wtt_oauth_credentials_to_hash( $auth ) === wpt_get_user_verification( $auth ) );
		if ( ! $return && 'verify' !== $context ) {
			return ( wtt_oauth_credentials_to_hash() === get_option( 'wtt_oauth_hash' ) );
		} else {
			return $return;
		}
	}
}

/**
 * Get user verification hash.
 *
 * @param mixed int $auth Current author.
 *
 * @return author hash.
 */
function wpt_get_user_verification( $auth ) {
	if ( get_option( 'jd_individual_twitter_users' ) !== '1' ) {
		return false;
	} else {
		$auth = get_user_meta( $auth, 'wtt_oauth_hash', true );

		return $auth;
	}
}

/**
 * Establish an OAuth connection to Twitter.
 *
 * @param mixed int/boolean $auth Current author.
 *
 * @return mixed $connection or false
 */
function wpt_oauth_connection( $auth = false ) {
	if ( ! $auth ) {
		$ack = get_option( 'app_consumer_key' );
		$acs = get_option( 'app_consumer_secret' );
		$ot  = get_option( 'oauth_token' );
		$ots = get_option( 'oauth_token_secret' );
	} else {
		$ack = get_user_meta( $auth, 'app_consumer_key', true );
		$acs = get_user_meta( $auth, 'app_consumer_secret', true );
		$ot  = get_user_meta( $auth, 'oauth_token', true );
		$ots = get_user_meta( $auth, 'oauth_token_secret', true );
	}
	if ( ! empty( $ack ) && ! empty( $acs ) && ! empty( $ot ) && ! empty( $ots ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-wpt-twitteroauth.php' );
		$connection            = new Wpt_TwitterOAuth( $ack, $acs, $ot, $ots );
		$connection->useragent = get_option( 'blogname' ) . ' ' . home_url();

		return $connection;
	} else {
		return false;
	}
}

/**
 * Convert oauth credentials to hash value for storage.
 *
 * @param mixed int/boolean $auth Author.
 *
 * @return hash.
 */
function wtt_oauth_credentials_to_hash( $auth = false ) {
	if ( ! $auth ) {
		$hash = md5( get_option( 'app_consumer_key' ) . get_option( 'app_consumer_secret' ) . get_option( 'oauth_token' ) . get_option( 'oauth_token_secret' ) );
	} else {
		$hash = md5( get_user_meta( $auth, 'app_consumer_key', true ) . get_user_meta( $auth, 'app_consumer_secret', true ) . get_user_meta( $auth, 'oauth_token', true ) . get_user_meta( $auth, 'oauth_token_secret', true ) );
	}

	return $hash;
}

/**
 * Update OAuth settings.
 *
 * @param mixed int/boolean   $auth Author.
 * @param mixed array/boolean $post POST data.
 */
function wpt_update_oauth_settings( $auth = false, $post = false ) {
	if ( isset( $post['oauth_settings'] ) ) {
		switch ( $post['oauth_settings'] ) {
			case 'wtt_oauth_test':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				if ( ! empty( $post['wtt_app_consumer_key'] )
					&& ! empty( $post['wtt_app_consumer_secret'] )
					&& ! empty( $post['wtt_oauth_token'] )
					&& ! empty( $post['wtt_oauth_token_secret'] )
				) {
					$ack = sanitize_text_field( trim( $post['wtt_app_consumer_key'] ) );
					$acs = sanitize_text_field( trim( $post['wtt_app_consumer_secret'] ) );
					$ot  = sanitize_text_field( trim( $post['wtt_oauth_token'] ) );
					$ots = sanitize_text_field( trim( $post['wtt_oauth_token_secret'] ) );
					if ( ! $auth ) {
						// If values are filled with asterisks, do not update; these are masked values.
						if ( stripos( $ack, '***' ) === false ) {
							update_option( 'app_consumer_key', $ack );
						}
						if ( stripos( $acs, '***' ) === false ) {
							update_option( 'app_consumer_secret', $acs );
						}
						if ( stripos( $ot, '***' ) === false ) {
							update_option( 'oauth_token', $ot );
						}
						if ( stripos( $ots, '***' ) === false ) {
							update_option( 'oauth_token_secret', $ots );
						}
					} else {
						if ( stripos( $ack, '***' ) === false ) {
							update_user_meta( $auth, 'app_consumer_key', $ack );
						}
						if ( stripos( $acs, '***' ) === false ) {
							update_user_meta( $auth, 'app_consumer_secret', $acs );
						}
						if ( stripos( $ot, '***' ) === false ) {
							update_user_meta( $auth, 'oauth_token', $ot );
						}
						if ( stripos( $ots, '***' ) === false ) {
							update_user_meta( $auth, 'oauth_token_secret', $ots );
						}
					}
					$message    = 'failed';
					$connection = wpt_oauth_connection( $auth );
					if ( $connection ) {
						$data = $connection->get( 'https://api.twitter.com/1.1/account/verify_credentials.json' );
						if ( '200' !== (string) $connection->http_code ) {
							$data  = json_decode( $data );
							$code  = "<a href='https://dev.twitter.com/docs/error-codes-responses'>" . $data->errors[0]->code . '</a>';
							$error = $data->errors[0]->message;
							update_option( 'wpt_error', "$code: $error" );
						} else {
							delete_option( 'wpt_error' );
						}
						if ( '200' === (string) $connection->http_code ) {
							$error_information = '';
							$decode            = json_decode( $data );
							if ( ! $auth ) {
								update_option( 'wtt_twitter_username', sanitize_text_field( stripslashes( $decode->screen_name ) ) );
							} else {
								update_user_meta( $auth, 'wtt_twitter_username', sanitize_text_field( stripslashes( $decode->screen_name ) ) );
							}
							$oauth_hash = wtt_oauth_credentials_to_hash( $auth );
							if ( ! $auth ) {
								update_option( 'wtt_oauth_hash', $oauth_hash );
							} else {
								update_user_meta( $auth, 'wtt_oauth_hash', $oauth_hash );
							}
							$message = 'success';
							delete_option( 'wpt_curl_error' );
						} elseif ( '0' === (string) $connection->http_code ) {
							$error_information = __( 'WP to Twitter was unable to establish a connection to Twitter.', 'wp-to-twitter' );
							update_option( 'wpt_curl_error', "$error_information" );
						} else {
							$status            = ( isset( $connection->http_header['status'] ) ) ? $connection->http_header['status'] : '404';
							$error_information = array(
								'http_code' => $connection->http_code,
								'status'    => $status,
							);
							// Translators: HTTP code & status message from Twitter.
							$error_code = sprintf( __( 'Twitter response: http_code %s', 'wp-to-twitter' ), "$error_information[http_code] - $error_information[status]" );
							update_option( 'wpt_curl_error', $error_code );
						}
						if ( '1' === get_option( 'wp_debug_oauth' ) ) {
							echo '<pre><strong>Summary Connection Response:</strong><br />';
							print_r( $error_information );
							echo '<br /><strong>Account Verification Data:</strong><br />';
							print_r( $data );
							echo '<br /><strong>Full Connection Response:</strong><br />';
							print_r( $connection );
							echo '</pre>';
						}
					} else {
						$message = 'noconnection';
					}
				} else {
					$message = 'nodata';
				}
				if ( 'failed' === $message && ( time() < strtotime( $connection->http_header['date'] ) - 300 || time() > strtotime( $connection->http_header['date'] ) + 300 ) ) {
					$message = 'nosync';
				}

				return $message;
				break;
			case 'wtt_twitter_disconnect':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				if ( ! $auth ) {
					update_option( 'app_consumer_key', '' );
					update_option( 'app_consumer_secret', '' );
					update_option( 'oauth_token', '' );
					update_option( 'oauth_token_secret', '' );
					update_option( 'wtt_twitter_username', '' );
				} else {
					delete_user_meta( $auth, 'app_consumer_key' );
					delete_user_meta( $auth, 'app_consumer_secret' );
					delete_user_meta( $auth, 'oauth_token' );
					delete_user_meta( $auth, 'oauth_token_secret' );
					delete_user_meta( $auth, 'wtt_twitter_username' );
				}
				$message = 'cleared';

				return $message;
				break;
		}
	}

	return '';
}

/**
 * Connect or disconnect from OAuth form.
 *
 * @param mixed int/boolean $auth Current author.
 */
function wtt_connect_oauth( $auth = false ) {
	if ( ! $auth ) {
		echo '<div class="ui-sortable meta-box-sortables">';
		echo '<div class="postbox">';
	}

	if ( $auth ) {
		wpt_update_authenticated_users();
	}

	$class = ( $auth ) ? 'wpt-profile' : 'wpt-settings';
	$form  = ( ! $auth ) ? '<form action="" method="post">' : '';
	$nonce = ( ! $auth ) ? wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, false ) . wp_referer_field( false ) . '</form>' : '';

	if ( ! wtt_oauth_test( $auth, 'verify' ) ) {

		// show notification to authenticate with OAuth. No longer global; settings only.
		if ( ! wpt_check_oauth() && ! ( isset( $_GET['tab'] ) && 'connection' === $_GET['tab'] ) ) {
			$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
			// Translators: Settings page to authenticate via OAuth.
			$message = sprintf( __( "Twitter requires authentication by OAuth. You will need to <a href='%s'>update your settings</a> to complete installation of WP to Twitter.", 'wp-to-twitter' ), $admin_url );
			echo "<div class='error'><p>$message</p></div>";
		}

		$ack = ( ! $auth ) ? get_option( 'app_consumer_key' ) : get_user_meta( $auth, 'app_consumer_key', true );
		$acs = ( ! $auth ) ? get_option( 'app_consumer_secret' ) : get_user_meta( $auth, 'app_consumer_secret', true );
		$ot  = ( ! $auth ) ? get_option( 'oauth_token' ) : get_user_meta( $auth, 'oauth_token', true );
		$ots = ( ! $auth ) ? get_option( 'oauth_token_secret' ) : get_user_meta( $auth, 'oauth_token_secret', true );

		$submit = ( ! $auth ) ? '<p class="submit"><input type="submit" name="submit" class="button-primary" value="' . __( 'Connect to Twitter', 'wp-to-twitter' ) . '" /></p>' : '';
		print( '
			<h3><span>' . __( 'Connect to Twitter', 'wp-to-twitter' ) . '</span></h3>
			<div class="inside ' . $class . '">
				<ol class="wpt-oauth-settings">
					<li>' . __( 'Apply for a <a href="https://developer.twitter.com/en/apply-for-access">Developer Account with Twitter</a>', 'wp-to-twitter' ) . '<ul>
						<li><a href="https://developer.twitter.com/en/developer-terms/policy">' . __( 'Review the Terms of Service for use of the Twitter API', 'wp-to-twitter' ) . '</a></li>
						<li>' . __( 'If your app is suspended by Twitter, contact <a href="https://help.twitter.com/forms/platform">their API Policy Support</a>.', 'wp-to-twitter' ) . '</li>
					</ul></li>
					<li>' . __( 'Add a new application in <a href="https://developer.twitter.com/en/portal/apps/new">Twitter\'s project and app portal</a>', 'wp-to-twitter' ) . '
						<ul>
							<li>' . __( 'Name your application.', 'wp-to-twitter' ) . '(' . __( 'Your app name cannot include the word "Twitter."', 'wp-to-twitter' ) . ')</li>
							<li>' . __( 'Click "Next" to move to the Keys & Tokens step.', 'wp-to-twitter' ) . '</li>
						</ul>
					</li>
					<li>' . __( 'Copy your API Key and API Key secret.', 'wp-to-twitter' ) . '
			' . $form . '
					<div class="tokens">
					<p>
						<label for="wtt_app_consumer_key">' . __( 'API Key', 'wp-to-twitter' ) . '</label>
						<input type="text" size="45" name="wtt_app_consumer_key" id="wtt_app_consumer_key" value="' . esc_attr( wpt_mask_attr( $ack ) ) . '" />
					</p>
					<p>
						<label for="wtt_app_consumer_secret">' . __( 'API Key Secret', 'wp-to-twitter' ) . '</label>
						<input type="text" size="45" name="wtt_app_consumer_secret" id="wtt_app_consumer_secret" value="' . esc_attr( wpt_mask_attr( $acs ) ) . '" />
					</p>
					</div>
					</li>
					<li>' . __( 'Click "App Settings" to configure your app', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Click "Set up" to configure User authentication settings', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Enable OAuth 1.0a', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Set "App Permissions" to "Read and write".', 'wp-to-twitter' ) . '</li>' .
					// Translators: Site URL.
					'<li>' . sprintf( __( 'Add your website as the Website URL and the Callback URI: %s', 'wp-to-twitter' ), '<code>' . home_url() . '</code>' ) . '</li>
					<li>' . __( 'Click "Save" to save settings.', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Click "Edit" at top of Settings screen to edit your App', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Change to the "Keys and Tokens" tab', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Generate your Access Token and Secret from the "Authentication Tokens" section.', 'wp-to-twitter' ) . '</li>
					<li>' . __( 'Paste your Access Token and Secret into the fields below', 'wp-to-twitter' ) . ' (' . __( 'If the Access Level for your Access Token is not "<em>Read and write</em>", return to step 7, change your permissions, and generate new Tokens.', 'wp-to-twitter' ) . ')
					<div class="tokens">
					<p>
						<label for="wtt_oauth_token">' . __( 'Access Token', 'wp-to-twitter' ) . '</label>
						<input type="text" size="45" name="wtt_oauth_token" id="wtt_oauth_token" value="' . esc_attr( wpt_mask_attr( $ot ) ) . '" />
					</p>
					<p>
						<label for="wtt_oauth_token_secret">' . __( 'Access Token Secret', 'wp-to-twitter' ) . '</label>
						<input type="text" size="45" name="wtt_oauth_token_secret" id="wtt_oauth_token_secret" value="' . esc_attr( wpt_mask_attr( $ots ) ) . '" />
					</p>
					</div>
				' . $submit . '
				<input type="hidden" name="oauth_settings" value="wtt_oauth_test" class="hidden" style="display: none;" />
				' . $nonce . '
			</div>
			</li>
			</ol>
				' );
	} elseif ( wtt_oauth_test( $auth ) ) {
		$ack   = ( ! $auth ) ? get_option( 'app_consumer_key' ) : get_user_meta( $auth, 'app_consumer_key', true );
		$acs   = ( ! $auth ) ? get_option( 'app_consumer_secret' ) : get_user_meta( $auth, 'app_consumer_secret', true );
		$ot    = ( ! $auth ) ? get_option( 'oauth_token' ) : get_user_meta( $auth, 'oauth_token', true );
		$ots   = ( ! $auth ) ? get_option( 'oauth_token_secret' ) : get_user_meta( $auth, 'oauth_token_secret', true );
		$uname = ( ! $auth ) ? get_option( 'wtt_twitter_username' ) : get_user_meta( $auth, 'wtt_twitter_username', true );
		$nonce = ( ! $auth ) ? wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, false ) . wp_referer_field( false ) . '</form>' : '';
		if ( ! $auth ) {
			$submit = '<input type="submit" name="submit" class="button-primary" value="' . __( 'Disconnect your WordPress and Twitter Account', 'wp-to-twitter' ) . '" />
					<input type="hidden" name="oauth_settings" value="wtt_twitter_disconnect" class="hidden" />';
		} else {
			$submit = '<input type="checkbox" name="oauth_settings" value="wtt_twitter_disconnect" id="disconnect" /> <label for="disconnect">' . __( 'Disconnect your WordPress and Twitter Account', 'wp-to-twitter' ) . '</label>';
		}

		print( '
			<h3><span>' . __( 'Disconnect from Twitter', 'wp-to-twitter' ) . '</span></h3>
			<div class="inside ' . $class . '">
			' . $form . '
				<div id="wtt_authentication_display">
					<fieldset class="options">
					<ul>
						<li><strong class="auth_label">' . __( 'Twitter Username ', 'wp-to-twitter' ) . '</strong> <code class="auth_code"><a href="http://twitter.com/' . esc_attr( $uname ) . '">' . esc_attr( $uname ) . '</a></code></li>
						<li><strong class="auth_label">' . __( 'API Key ', 'wp-to-twitter' ) . '</strong> <code class="auth_code">' . esc_attr( wpt_mask_attr( $ack ) ) . '</code></li>
						<li><strong class="auth_label">' . __( 'API Secret ', 'wp-to-twitter' ) . '</strong> <code class="auth_code">' . esc_attr( wpt_mask_attr( $acs ) ) . '</code></li>
						<li><strong class="auth_label">' . __( 'Access Token ', 'wp-to-twitter' ) . '</strong> <code class="auth_code">' . esc_attr( wpt_mask_attr( $ot ) ) . '</code></li>
						<li><strong class="auth_label">' . __( 'Access Token Secret ', 'wp-to-twitter' ) . '</strong> <code class="auth_code">' . esc_attr( wpt_mask_attr( $ots ) ) . '</code></li>
					</ul>
					</fieldset>
					<div>
					' . $submit . '
					</div>
				</div>
				' . $nonce . '
			</div>' );

	}
	if ( ! $auth ) {
		echo '</div>
		</div>';
	}
}

/**
 * Mask secure values.
 *
 * @param string $value Original value.
 *
 * @return string
 */
function wpt_mask_attr( $value ) {
	$count  = strlen( $value );
	$substr = substr( $value, -5 );
	$return = str_pad( $substr, $count, '*', STR_PAD_LEFT );

	return $return;
}

/**
 * Update stored set of authenticated users.
 */
function wpt_update_authenticated_users() {
	$args = array(
		'meta_query' => array(
			array(
				'key'     => 'wtt_twitter_username',
				'compare' => 'EXISTS',
			),
		),
	);
	// get all authorized users.
	$users            = get_users( $args );
	$authorized_users = array();
	if ( is_array( $users ) ) {
		foreach ( $users as $this_user ) {
			if ( wtt_oauth_test( $this_user->ID, 'verify' ) ) {
				$twitter            = get_user_meta( $this_user->ID, 'wtt_twitter_username', true );
				$authorized_users[] = array(
					'ID'      => $this_user->ID,
					'name'    => $this_user->display_name,
					'twitter' => $twitter,
				);
			}
		}
	}

	update_option( 'wpt_authorized_users', $authorized_users );
}
