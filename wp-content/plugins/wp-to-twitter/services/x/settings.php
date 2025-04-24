<?php
/**
 * Connect OAuth for XPoster
 *
 * @category OAuth
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.xposterpro.com
 */

use WpToTwitter_Vendor\Noweh\TwitterApi\Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check for OAuth configuration
 *
 * @param int|boolean $auth Which user ID to check. False for main account.
 *
 * @return boolean True if authorized.
 */
function wpt_check_oauth( $auth = false ) {
	if ( ! function_exists( 'wtt_oauth_test' ) ) {
		$oauth = false;
	} else {
		$oauth = wtt_oauth_test( $auth );
	}

	return $oauth;
}

/**
 * Function to test validity of credentials
 *
 * @param int|boolean $auth Current author.
 * @param string      $context Use context.
 *
 * @return bool Is authenticated.
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
 * Establish an OAuth client to X.com.
 *
 * @param mixed int/boolean $auth Current author.
 * @param mixed string      $api API version.
 *
 * @return mixed $client or false
 */
function wpt_oauth_connection( $auth = false, $api = '2' ) {
	if ( ! $auth ) {
		$ack     = get_option( 'app_consumer_key' );
		$acs     = get_option( 'app_consumer_secret' );
		$ot      = get_option( 'oauth_token' );
		$ots     = get_option( 'oauth_token_secret' );
		$bt      = get_option( 'bearer_token' );
		$account = get_option( 'wtt_twitter_username' );
	} else {
		$ack     = get_user_meta( $auth, 'app_consumer_key', true );
		$acs     = get_user_meta( $auth, 'app_consumer_secret', true );
		$ot      = get_user_meta( $auth, 'oauth_token', true );
		$ots     = get_user_meta( $auth, 'oauth_token_secret', true );
		$bt      = get_user_meta( $auth, 'bearer_token', true );
		$account = get_user_meta( $auth, 'wtt_twitter_username', true );
	}
	if ( ! empty( $ack ) && ! empty( $acs ) && ! empty( $ot ) && ! empty( $ots ) ) {
		if ( '2' === $api ) {
			$settings = array(
				'account_id'          => $account,
				'access_token'        => $ot,
				'access_token_secret' => $ots,
				'consumer_key'        => $ack,
				'consumer_secret'     => $acs,
				'bearer_token'        => $bt,
			);
			$client   = new Client( $settings );
		} else {
			require_once plugin_dir_path( __FILE__ ) . 'class-wpt-twitteroauth.php';
			$connection            = new Wpt_TwitterOAuth( $ack, $acs, $ot, $ots );
			$connection->useragent = get_option( 'blogname' ) . ' ' . home_url();
			$client                = $connection;
		}

		return $client;
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
				// Save bearer token. New field, existing users won't have it.
				if ( ! empty( $post['wtt_bearer_token'] ) ) {
					$bt = sanitize_text_field( trim( $post['wtt_bearer_token'] ) );
					if ( ! $auth ) {
						if ( stripos( $bt, '***' ) === false ) {
							update_option( 'bearer_token', $bt );
						}
					} else {
						if ( stripos( $bt, '***' ) === false ) {
							update_user_meta( $auth, 'bearer_token', $bt );
						}
					}
				}
				if ( ! empty( $post['wtt_app_consumer_key'] )
					&& ! empty( $post['wtt_app_consumer_secret'] )
					&& ! empty( $post['wtt_oauth_token'] )
					&& ! empty( $post['wtt_oauth_token_secret'] )
					&& ! empty( $post['wtt_bearer_token'] )
				) {
					$ack = sanitize_text_field( trim( $post['wtt_app_consumer_key'] ) );
					$acs = sanitize_text_field( trim( $post['wtt_app_consumer_secret'] ) );
					$ot  = sanitize_text_field( trim( $post['wtt_oauth_token'] ) );
					$ots = sanitize_text_field( trim( $post['wtt_oauth_token_secret'] ) );
					$bt  = sanitize_text_field( trim( $post['wtt_bearer_token'] ) );

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
						if ( stripos( $bt, '***' ) === false ) {
							update_option( 'bearer_token', $bt );
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
						if ( stripos( $bt, '***' ) === false ) {
							update_user_meta( $auth, 'bearer_token', $bt );
						}
					}
					$message    = 'failed';
					$connection = wpt_oauth_connection( $auth, '1.1' );
					if ( $connection ) {
						$data = $connection->get( 'https://api.x.com/1.1/account/verify_credentials.json' );
						if ( '200' !== (string) $connection->http_code ) {
							$parsed = json_decode( $data );
							if ( is_object( $parsed ) ) {
								$code  = "<a href='https://developer.x.com/en/support/x-api/error-troubleshooting'>" . $parsed->errors[0]->code . '</a>';
								$error = $parsed->errors[0]->message;
							} else {
								$code  = 'null';
								$error = __( 'Data was not returned in a recognizable format.', 'wp-to-twitter' );
							}
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
							$error_information = __( 'XPoster was unable to establish a connection to X.com.', 'wp-to-twitter' );
							update_option( 'wpt_curl_error', "$error_information" );
						} else {
							$status            = ( isset( $connection->http_header['status'] ) ) ? $connection->http_header['status'] : '404';
							$error_information = array(
								'http_code' => $connection->http_code,
								'status'    => $status,
							);
							// Translators: HTTP code & status message from X.com.
							$error_code = sprintf( __( 'X.com response: http_code %s', 'wp-to-twitter' ), "$error_information[http_code] - $error_information[status]" );
							update_option( 'wpt_curl_error', $error_code );
						}
						if ( '1' === get_option( 'wp_debug_oauth' ) ) {
							echo '<h2>Summary Connection Response</h2><pre>';
							wpt_format_error( $error_information );
							echo '</pre><h2>Account Verification Data</h2><pre>';
							wpt_format_error( $data );
							echo '</pre><h2>Full Connection Response</h2><pre>';
							wpt_format_error( $connection );
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
			case 'wtt_x_update':
				if ( ! wp_verify_nonce( $post['_wpnonce'], 'wp-to-twitter-nonce' ) && ! $auth ) {
					wp_die( 'Oops, please try again.' );
				}
				$option  = get_option( 'wpt_disabled_services', array() );
				$disable = isset( $post['wpt_disabled_services'] ) ? true : false;
				if ( $disable ) {
					$option['x'] = 'true';
				} else {
					unset( $option['x'] );
				}
				if ( isset( $_POST['wpt_x_length'] ) ) {
					update_option( 'wpt_x_length', intval( $_POST['wpt_x_length'] ) );
				}
				update_option( 'wpt_disabled_services', $option );
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
					update_option( 'bearer_token', '' );
					update_option( 'wtt_twitter_username', '' );
				} else {
					delete_user_meta( $auth, 'app_consumer_key' );
					delete_user_meta( $auth, 'app_consumer_secret' );
					delete_user_meta( $auth, 'oauth_token' );
					delete_user_meta( $auth, 'oauth_token_secret' );
					delete_user_meta( $auth, 'bearer_token' );
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

	if ( ! wtt_oauth_test( $auth, 'verify' ) ) {
		$ack = ( ! $auth ) ? get_option( 'app_consumer_key' ) : get_user_meta( $auth, 'app_consumer_key', true );
		$acs = ( ! $auth ) ? get_option( 'app_consumer_secret' ) : get_user_meta( $auth, 'app_consumer_secret', true );
		$ot  = ( ! $auth ) ? get_option( 'oauth_token' ) : get_user_meta( $auth, 'oauth_token', true );
		$ots = ( ! $auth ) ? get_option( 'oauth_token_secret' ) : get_user_meta( $auth, 'oauth_token_secret', true );
		$bt  = ( ! $auth ) ? get_option( 'bearer_token' ) : get_user_meta( $auth, 'bearer_token', true );
		?>

		<h3 class="wpt-has-link"><span><?php esc_html_e( 'Connect to X.com', 'wp-to-twitter' ); ?></span> <a href="https://xposterpro.com/connecting-xposter-and-x-com/" class="button button-secondary"><?php esc_html_e( 'Instructions', 'wp-to-twitter' ); ?></a></h3>
		<div class="inside <?php echo esc_attr( $class ); ?>">
		<?php
		echo ( ! $auth ) ? '<form action="" method="post" class="wpt-connection-form">' : '';
		?>
			<ol class="wpt-oauth-settings">
				<li><?php echo wp_kses_post( __( 'Apply for a <a href="https://developer.twitter.com/en/apply-for-access">Developer Account with X.com</a>', 'wp-to-twitter' ) ); ?><ul>
					<li><a href="https://developer.twitter.com/en/developer-terms/policy"><?php esc_html_e( 'Review the Terms of Service for use of the X.com API', 'wp-to-twitter' ); ?></a></li>
					<li><?php echo wp_kses_post( __( 'If your app is suspended by X.com, contact <a href="https://help.twitter.com/forms/platform">their API Policy Support</a>.', 'wp-to-twitter' ) ); ?></li>
				</ul></li>
				<li><?php echo wp_kses_post( __( 'Add a new application in <a href="https://developer.twitter.com/en/portal/apps/new">X.com\'s project and app portal</a>', 'wp-to-twitter' ) ); ?>
					<ul>
						<li><?php esc_html_e( 'Name your application.', 'wp-to-twitter' ); ?> (<?php esc_html_e( 'Your app name cannot include the word "Twitter."', 'wp-to-twitter' ); ?>)</li>
						<li><?php esc_html_e( 'Click "Next" to move to the Keys & Tokens step.', 'wp-to-twitter' ); ?></li>
					</ul>
				</li>
				<li><?php esc_html_e( 'Copy your API Key and API Key secret.', 'wp-to-twitter' ); ?>
				<div class="tokens auth-fields">
				<p>
					<label for="wtt_app_consumer_key"><?php esc_html_e( 'API Key', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wtt_app_consumer_key" id="wtt_app_consumer_key" value="<?php echo esc_attr( wpt_mask_attr( $ack ) ); ?>" />
				</p>
				<p>
					<label for="wtt_app_consumer_secret"><?php esc_html_e( 'API Key Secret', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wtt_app_consumer_secret" id="wtt_app_consumer_secret" value="<?php echo esc_attr( wpt_mask_attr( $acs ) ); ?>" />
				</p>
				</div>
				</li>
				<li><?php esc_html_e( 'Click "App Settings" to configure your app', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Click "Set up" to configure User authentication settings', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Enable OAuth 1.0a', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Set "App Permissions" to "Read and write".', 'wp-to-twitter' ); ?></li>
				<li>
				<?php
				// Translators: Site URL.
				echo wp_kses_post( sprintf( __( 'Add your website as the Website URL and the Callback URI: %s', 'wp-to-twitter' ), '<code>' . home_url() . '</code>' ) );
				?>
				</li>
				<li><?php esc_html_e( 'Click "Save" to save settings.', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Click "Edit" at top of Settings screen to edit your App', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Change to the "Keys and Tokens" tab', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Generate your Access Token and Secret from the "Authentication Tokens" section.', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Add your Access Token, Secret, and Bearer Token:', 'wp-to-twitter' ); ?> (<?php echo wp_kses_post( __( 'If the Access Level for your Access Token is not "<em>Read and write</em>", return to step 7, change your permissions, and generate new Tokens.', 'wp-to-twitter' ) ); ?>)
				<div class="tokens auth-fields">
				<p>
					<label for="wtt_oauth_token"><?php esc_html_e( 'Access Token', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wtt_oauth_token" id="wtt_oauth_token" value="<?php echo esc_attr( wpt_mask_attr( $ot ) ); ?>" />
				</p>
				<p>
					<label for="wtt_oauth_token_secret"><?php esc_html_e( 'Access Token Secret', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wtt_oauth_token_secret" id="wtt_oauth_token_secret" value="<?php echo esc_attr( wpt_mask_attr( $ots ) ); ?>" />
				</p>
				<p>
					<label for="wtt_bearer_token"><?php esc_html_e( 'Bearer Token', 'wp-to-twitter' ); ?></label>
					<input type="text" size="45" name="wtt_bearer_token" id="wtt_bearer_token" value="<?php echo esc_attr( wpt_mask_attr( $bt ) ); ?>" />
				</p>
				</div>
				</li>
				<li>
					<p><?php esc_html_e( 'Create a Project and add your App to the project.', 'wp-to-twitter' ); ?></p>
				</li>
			</ol>
			<?php
			if ( ! $auth ) {
				?>
				<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php esc_html_e( 'Connect to X.com', 'wp-to-twitter' ); ?>" /></p>
				<?php wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true ); ?>
				<?php
			}
			?>
			<input type="hidden" name="oauth_settings" value="wtt_oauth_test" class="hidden" />
		</div>
		<?php
	} elseif ( wtt_oauth_test( $auth ) ) {
		$ack   = ( ! $auth ) ? get_option( 'app_consumer_key' ) : get_user_meta( $auth, 'app_consumer_key', true );
		$acs   = ( ! $auth ) ? get_option( 'app_consumer_secret' ) : get_user_meta( $auth, 'app_consumer_secret', true );
		$ot    = ( ! $auth ) ? get_option( 'oauth_token' ) : get_user_meta( $auth, 'oauth_token', true );
		$ots   = ( ! $auth ) ? get_option( 'oauth_token_secret' ) : get_user_meta( $auth, 'oauth_token_secret', true );
		$bt    = ( ! $auth ) ? get_option( 'bearer_token' ) : get_user_meta( $auth, 'bearer_token', true );
		$uname = ( ! $auth ) ? get_option( 'wtt_twitter_username' ) : get_user_meta( $auth, 'wtt_twitter_username', true );
		$site  = get_bloginfo( 'name' );
		?>

		<h3><?php esc_html_e( 'X Connection', 'wp-to-twitter' ); ?></h3>
		<div class="inside <?php echo esc_attr( $class ); ?>">
		<?php
		if ( ! $bt ) {
			?>
			<p><?php esc_html_e( 'The X.com version 2 API requires an additional API setting in your connection settings.', 'wp-to-twitter' ); ?></p>
			<ol>
				<li><?php echo wp_kses_post( __( 'Go to <a href="https://developer.twitter.com/en/portal/dashboard">the X.com Developer Dashboard</a>', 'wp-to-twitter' ) ); ?></li>
				<li><?php esc_html_e( 'If prompted, create a project to use v2 endpoints. Follow the prompts to use your app.', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Open your existing App.', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Choose the Keys and Tokens tab', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Generate the Bearer Token', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'Copy and Save your Bearer Token', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'If you already had a project, assign your app to the project.', 'wp-to-twitter' ); ?></li>
			</ol>
			<?php
		}
		echo ( ! $auth ) ? '<form action="" method="post" class="wpt-connection-form">' : '';
		if ( ! $bt ) {
			?>
			<p>
				<label for="wtt_bearer_token"><?php esc_html_e( 'Bearer Token', 'wp-to-twitter' ); ?></label>
				<input type="text" size="45" name="wtt_bearer_token" id="wtt_bearer_token" value="<?php echo esc_attr( wpt_mask_attr( $bt ) ); ?>" />
			</p>
			<?php
		}
		?>
			<div id="wtt_authentication_display">
				<ul>
					<li><strong class="auth_label"><?php esc_html_e( 'Username ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><a href="http://twitter.com/<?php echo esc_attr( $uname ); ?>"><?php echo esc_attr( $uname ); ?></a></code></li>
					<li><strong class="auth_label"><?php esc_html_e( 'API Key ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $ack ) ); ?></code></li>
					<li><strong class="auth_label"><?php esc_html_e( 'API Secret ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $acs ) ); ?></code></li>
					<li><strong class="auth_label"><?php esc_html_e( 'Access Token ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $ot ) ); ?></code></li>
					<li><strong class="auth_label"><?php esc_html_e( 'Access Token Secret ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $ots ) ); ?></code></li>
					<?php
					if ( $bt ) {
						?>
					<li><strong class="auth_label"><?php esc_html_e( 'Bearer Token ', 'wp-to-twitter' ); ?></strong> <code class="auth_code"><?php echo esc_attr( wpt_mask_attr( $bt ) ); ?></code></li>
						<?php
					}
					?>
				</ul>
			<div>
			<?php
			if ( ! $auth ) {
				// Translators: Name of the current site.
				$text = sprintf( __( 'Disconnect %s from X', 'wp-to-twitter' ), $site );
				?>
				<input type="submit" name="submit" class="button-primary" value="<?php echo esc_attr( $text ); ?>" />
				<input type="hidden" name="oauth_settings" value="wtt_twitter_disconnect" class="hidden" />
				<?php
				wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true );
			} else {
				?>
				<input type="checkbox" name="oauth_settings" value="wtt_twitter_disconnect" id="disconnect_x" /> <label for="disconnect_x"><?php esc_html_e( 'Disconnect Your Account from X', 'wp-to-twitter' ); ?></label>
				<?php
			}
			echo ( ! $auth ) ? '</form>' : '';
			?>
			</div>
		</div>
		<?php
		if ( ! $auth ) {
			$disabled = get_option( 'wpt_disabled_services', array() );
			$checked  = ( in_array( 'x', array_keys( $disabled ), true ) ) ? 'checked' : '';
			?>
			<form action="" method="post" class="wpt-connection-form">
				<?php wpt_service_length( 'x' ); ?>
				<p class="checkboxes"><input <?php checked( 'checked', $checked ); ?> type="checkbox" name="wpt_disabled_services[]" id="wpt_disable_x" value="x"><label for="wpt_disable_x"><?php esc_html_e( 'Disable Posting to X', 'wp-to-twitter' ); ?></label></p>
				<input type="hidden" name="oauth_settings" value="wtt_x_update"><input type="submit" name="wtt_x_update" class="button-secondary" value="<?php esc_html_e( 'Save Changes', 'wp-to-twitter' ); ?>" />
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
