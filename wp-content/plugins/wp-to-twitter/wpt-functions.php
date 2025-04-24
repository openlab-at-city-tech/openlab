<?php
/**
 * Core support functions XPoster
 *
 * @category Core
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/classes/class-wpt-normalizer.php';

/**
 * See if checkboxes should be checked
 *
 * @param string $field Option name to check.
 * @param string $sub1 Array key if applicable.
 * @param string $sub2 Array key if applicable.
 *
 * @return Checked or unchecked.
 */
function wpt_checkbox( $field, $sub1 = false, $sub2 = '' ) {
	if ( $sub1 ) {
		$setting = get_option( $field );
		if ( isset( $setting[ $sub1 ] ) ) {
			$value = ( '' !== $sub2 ) ? $setting[ $sub1 ][ $sub2 ] : $setting[ $sub1 ];
		} else {
			$value = 0;
		}
		if ( $value && 1 === (int) $value ) {
			return 'checked';
		} else {
			return '';
		}
	}
	if ( 1 === (int) get_option( $field ) ) {
		return 'checked';
	}

	return '';
}

/**
 * See if options should be selected. Note: does not appear to be in use as of 7/14/2019.
 *
 * @param string $field Option name to check.
 * @param string $value Value to verify against.
 * @param string $type Select or checkbox.
 *
 * @return Selected or unselected/ checked or unchecked.
 */
function wpt_selected( $field, $value, $type = 'select' ) {
	if ( get_option( $field ) === $value ) {
		return ( 'select' === $type ) ? 'selected="selected"' : 'checked="checked"';
	}
	return '';
}

/**
 * Insert a status update record into logs.
 *
 * @param string $data Option key.
 * @param int    $id Post ID.
 * @param string $message Log message.
 * @param string $http HTTP code for this message.
 */
function wpt_set_log( $data, $id, $message, $http = '200' ) {
	if ( 'test' === $id ) {
		delete_transient( $data );
		set_transient( $data, $message, 300 );
	} else {
		$message = array(
			'message' => $message,
			'http'    => (string) $http,
		);
		delete_transient( $id . '_' . $data );
		set_transient( $id . '_' . $data, $message );
	}
	delete_transient( $data . '_last' );
	set_transient( $data . '_last', array( $id, $message ), 300 );
}

/**
 * Get information from status update logs.
 *
 * @param string $data Option key.
 * @param int    $id Post ID.
 *
 * @return string|array message.
 */
function wpt_get_log( $data, $id ) {
	if ( 'test' === $id ) {
		$log = get_transient( $data );
	} elseif ( 'last' === $id ) {
		$log = get_transient( $data . '_last' );
	} else {
		$log = get_transient( $id . '_' . $data );
	}

	return $log;
}

/**
 * Test function to see whether options are functioning.
 */
function wpt_check_functions() {
	$message = "<div class='update'><ul>";
	// grab or set necessary variables.
	$testurl  = get_bloginfo( 'url' );
	$testpost = false;
	$title    = urlencode( 'Your blog home' );
	/**
	 * Filter the URL passed when running a test function.
	 *
	 * @hook wptt_shorten_link
	 *
	 * @param {string}   $testurl Unshortened test URL.
	 * @param {string}   $title Title text to use for URL shortener.
	 * @param {int|bool} $post_ID Post ID. Default false.
	 * @param {bool}     $testing In testing mode. Default true.
	 *
	 * @return {string}
	 */
	$shrink = apply_filters( 'wptt_shorten_link', $testurl, $title, false, true );
	if ( false === $shrink ) {
		$error    = htmlentities( get_option( 'wpt_shortener_status' ) );
		$message .= '<li class="error"><strong>' . __( 'XPoster was unable to contact your selected URL shortening service.', 'wp-to-twitter' ) . '</strong></li>';
		if ( is_string( $error ) && strlen( trim( $error ) ) > 0 ) {
			$message .= "<li><code>$error</code></li>";
		} else {
			$message .= '<li><code>' . __( 'No error message was returned.', 'wp-to-twitter' ) . '</code></li>';
		}
	} else {
		$message .= '<li><strong>' . __( "XPoster successfully contacted your URL shortening service.</strong>  This link should point to your site's homepage:", 'wp-to-twitter' );
		$message .= " <a href='$shrink'>$shrink</a></li>";
	}
	// check social network credentials.
	if ( wpt_check_connections( false, true ) ) {
		$rand     = wp_rand( 1000000, 9999999 );
		$testpost = wpt_post_to_service( "This is a test of XPoster. $shrink ($rand)" );
		if ( $testpost && ! empty( $testpost ) ) {
			foreach ( $testpost as $key => $test ) {
				if ( 'xcom' === $key ) {
					$message .= '<li>' . __( 'XPoster successfully submitted a status update to X.com.', 'wp-to-twitter' ) . '</li>';
				}
				if ( 'mastodon' === $key ) {
					$message .= '<li>' . __( 'XPoster successfully submitted a status update to your Mastodon instance.', 'wp-to-twitter' ) . '</li>';
				}
				if ( 'bluesky' === $key ) {
					$message .= '<li>' . __( 'XPoster successfully submitted a status update to your Bluesky account.', 'wp-to-twitter' ) . '</li>';
				}
			}
		} else {
			$error    = wpt_get_log( 'wpt_status_message', 'test' );
			$message .= '<li class="error">' . __( 'XPoster failed to submit status updates.', 'wp-to-twitter' ) . '</li>';
			$message .= ( '' !== $error ) ? "<li class='error'>$error</li>" : '';
		}
	} else {
		$message .= __( 'You have not connected WordPress to a supported service.', 'wp-to-twitter' ) . ' ';
	}
	if ( false === $testpost && false === $shrink ) {
		$message .= '<li class="error">' . __( "<strong>Your server does not appear to support the required methods for XPoster to function.</strong> You can try it anyway - these tests aren't perfect.", 'wp-to-twitter' ) . '</li>';
	}
	if ( $testpost && $shrink ) {
		$message .= '<li>' . __( 'Your server should run XPoster successfully.', 'wp-to-twitter' ) . '</li>';
	}
	$message .= '</ul>
	</div>';

	return $message;
}

/**
 * Generate Settings links.
 */
function wpt_settings_tabs() {
	$username = get_option( 'wtt_twitter_username' );
	$default  = ( '' === $username || false === $username ) ? 'connection' : 'basic';
	$current  = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : $default;
	$pro_text = ( function_exists( 'wpt_pro_exists' ) ) ? __( 'Pro Settings', 'wp-to-twitter' ) : __( 'XPoster PRO', 'wp-to-twitter' );

	$yes      = '<span class="dashicons dashicons-yes" aria-label="Connected"></span>';
	$no       = '<span class="dashicons dashicons-no" aria=label="Unconnected"></span>';
	$disabled = '<span class="dashicons dashicons-hidden" aria-label="Disabled"></span>';
	if ( wtt_oauth_test() ) {
		$x_connected = ( wpt_service_enabled( false, 'x' ) ) ? $yes : $disabled;
	} else {
		$x_connected = $no;
	}
	if ( wpt_mastodon_connection() ) {
		$m_connected = ( wpt_service_enabled( false, 'mastodon' ) ) ? $yes : $disabled;
	} else {
		$m_connected = $no;
	}
	if ( wpt_bluesky_connection() ) {
		$b_connected = ( wpt_service_enabled( false, 'bluesky' ) ) ? $yes : $disabled;
	} else {
		$b_connected = $no;
	}

	$pages = array(
		'connection' => __( 'X', 'wp-to-twitter' ) . $x_connected,
		'mastodon'   => __( 'Mastodon', 'wp-to-twitter' ) . $m_connected,
		'bluesky'    => __( 'Bluesky', 'wp-to-twitter' ) . $b_connected,
		'basic'      => __( 'Settings', 'wp-to-twitter' ),
		'shortener'  => __( 'URL Shortener', 'wp-to-twitter' ),
		'advanced'   => __( 'Advanced Settings', 'wp-to-twitter' ),
		'support'    => __( 'Get Help', 'wp-to-twitter' ),
		'pro'        => $pro_text,
	);

	/**
	 * Filter the array of tabs representings settings pages.
	 *
	 * @hook wpt_settings_tabs_pages
	 *
	 * @param {array}  $pages Array of pages with `[ 'key' => 'title' ]`.
	 * @param {string} $current Array key of current page.
	 *
	 * @return {array}
	 */
	$pages     = apply_filters( 'wpt_settings_tabs_pages', $pages, $current );
	$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );

	foreach ( $pages as $key => $value ) {
		$selected = ( $key === $current ) ? ' nav-tab-active' : '';
		$url      = add_query_arg( 'tab', $key, $admin_url );
		if ( 'pro' === $key ) {
			$selected .= ' wpt-pro-tab';
		}
		?>
		<a class='nav-tab<?php echo esc_attr( $selected ); ?>' href='<?php echo esc_url( $url ); ?>'><?php echo wp_kses_post( $value ); ?></a>
		<?php

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
 * Show the last status update attempt as admin notice.
 */
function wpt_show_last_update() {
	/**
	 * Disable the admin notice that shows the last sent update.
	 *
	 * @hook wpt_show_last_update
	 *
	 * @param {bool} $show true to show; false to hide.
	 *
	 * @return {bool}
	 */
	if ( apply_filters( 'wpt_show_last_update', true ) ) {
		$log = wpt_get_log( 'wpt_status_message', 'last' );
		if ( ! empty( $log ) && is_array( $log ) ) {
			$post_ID = $log[0];
			$post    = get_post( $post_ID );
			if ( is_object( $post ) ) {
				$title = "<a href='" . esc_url( get_edit_post_link( $post_ID ) ) . "'>" . esc_html( $post->post_title ) . '</a>';
			} else {
				$title = '(' . __( 'No post', 'wp-to-twitter' ) . ')';
			}
			if ( is_array( $log[1] ) ) {
				$notice = esc_html( $log[1]['message'] );
				$code   = esc_html( $log[1]['http'] );
			} elseif ( is_string( $log[1] ) ) {
				$notice = esc_html( $log[1] );
				$code   = '';
			} else {
				$notice = __( 'Unrecognized error', 'wp-to-twitter' );
				$code   = '';
			}
			$message = '<strong>' . __( 'Last Status Update', 'wp-to-twitter' ) . ": <code>$code</code></strong> $title &raquo; $notice";
			wp_admin_notice(
				$message,
				array(
					'type' => 'info',
				)
			);
		}
	}
}

/**
 * Handle Update & URL shortener errors.
 */
function wpt_handle_errors() {
	if ( isset( $_POST['submit-type'] ) && 'clear-error' === $_POST['submit-type'] ) {
		delete_option( 'wp_url_failure' );
	}
	if ( '1' === get_option( 'wp_url_failure' ) ) {
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		?>
		<div class="error">
			<p><?php esc_html_e( 'The query to the URL shortener API failed, and your URL was not shrunk. The full post URL was attached to your status update. Check with your URL shortening provider to see if there are any known issues.', 'wp-to-twitter' ); ?></p>
			<form method="post" action="<?php esc_url( $admin_url ); ?>">
				<div>
					<input type="hidden" name="submit-type" value="clear-error"/>
					<?php wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, true ); ?>
				</div>
				<p>
					<input type="submit" name="submit" value="<?php esc_html_e( 'Clear Error Messages', 'wp-to-twitter' ); ?>" class="button-primary" />
				</p>
			</form>
		</div>
		<?php
	}
}

/**
 * Verify user capabilities
 *
 * @param string $role Role name.
 * @param string $cap Capability name.
 *
 * @return string 'checked' if has capability.
 */
function wpt_check_caps( $role, $cap ) {
	$role = get_role( $role );
	if ( $role->has_cap( $cap ) ) {
		return 'checked';
	}
	return '';
}

/**
 * Output checkbox for user capabilities
 *
 * @param string $role Role name.
 * @param string $cap Capability name.
 * @param string $name Display name for capability.
 */
function wpt_cap_checkbox( $role, $cap, $name ) {
	$id       = 'wpt_caps_' . $role . '_' . $cap;
	$has_caps = wpt_check_caps( $role, $cap );
	?>
	<li>
		<input type='checkbox' id='<?php echo esc_attr( $id ); ?>' name='wpt_caps[<?php echo esc_attr( $role ); ?>][<?php echo esc_attr( $cap ); ?>]' value='on'<?php checked( 'checked', $has_caps ); ?> />
		<label for='<?php echo esc_attr( $id ); ?>'><?php echo esc_html( $name ); ?></label>
	</li>
	<?php
}

/**
 * Send a debug message. (Used email by default until 3.3.)
 *
 * @param string  $subject Subject of error.
 * @param string  $body Body of error.
 * @param int     $post_ID ID of Post being shared.
 * @param boolean $override Send message if debug disabled.
 */
function wpt_mail( $subject, $body, $post_ID = false, $override = false ) {
	$body .= ' Active Filter:' . current_filter();
	if ( ( WPT_DEBUG ) ) {
		if ( WPT_DEBUG_BY_EMAIL ) {
			wp_mail( WPT_DEBUG_ADDRESS, $subject, $body, WPT_FROM );
		} else {
			wpt_debug_log( $subject, $body, $post_ID );
		}
	}
}

/**
 * Insert record into debug log.
 *
 * @param string $subject Subject of error.
 * @param string $body Body of error.
 * @param int    $post_ID ID of post being shared.
 */
function wpt_debug_log( $subject, $body, $post_ID ) {
	if ( ! $post_ID ) {
		global $post_ID;
	}
	if ( $post_ID ) {
		$time = microtime();
		add_post_meta( $post_ID, '_wpt_debug_log', array( $time, $subject, $body ) );
	}
}

/**
 * Display debug log.
 */
function wpt_show_debug() {
	global $post_ID;
	if ( WPT_DEBUG ) {
		$records   = '';
		$debug_log = get_post_meta( $post_ID, '_wpt_debug_log' );

		if ( ! empty( $debug_log ) ) {
			?>
			<div class='wpt-debug-log'>
				<h3>Debugging Log:</h3>
				<ul>
					<?php
					if ( is_array( $debug_log ) && ! empty( $debug_log ) ) {
						foreach ( $debug_log as $entry ) {
							$microtime = $entry[0];
							$date      = explode( ' ', $microtime );
							if ( count( $date ) > 1 ) {
								$datetime = $date[1];
							} else {
								$datetime = $date[0];
							}
							$date    = date_i18n( 'Y-m-d H:i:s', $datetime );
							$subject = $entry[1];
							$body    = $entry[2];
							?>
							<li>
								<button type='button' class='toggle-debug button-secondary' aria-expanded='false'>
									<div>
										<strong><?php echo esc_html( $date ); ?></strong><br />
										<?php echo esc_html( $subject ); ?>
									</div>
									<span class='dashicons dashicons-plus' aria-hidden='true'></span>
								</button>
								<pre class='wpt-debug-details'><?php echo wp_kses_post( $body ); ?></pre>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
			<ul>
				<li><input type='checkbox' name='wpt-delete-debug' value='true' id='wpt-delete-debug'> <label for='wpt-delete-debug'><?php esc_html_e( 'Delete debugging logs on this post', 'wp-to-twitter' ); ?></label></li>
				<li><input type='checkbox' name='wpt-delete-all-debug' value='true' id='wpt-delete-all-debug'> <label for='wpt-delete-all-debug'><?php esc_html_e( 'Delete debugging logs for all posts', 'wp-to-twitter' ); ?></label></li>
			</ul>
			<?php
		}
	}
}

/**
 * Send a remote query expecting JSON.
 *
 * @param string    $url Target URL.
 * @param null|bool $args JSON decode arguments if not default.
 * @param string    $method Query method.
 * @throws Exception JSON error string.
 *
 * @return JSON object.
 */
function wpt_remote_json( $url, $args = true, $method = 'GET' ) {
	$input = wpt_fetch_url( $url, $method );
	$obj   = json_decode( $input, $args );
	if ( function_exists( 'json_last_error' ) ) { // > PHP 5.3.
		try {
			if ( is_null( $obj ) ) {
				switch ( json_last_error() ) {
					case JSON_ERROR_DEPTH:
						$msg = ' - Maximum stack depth exceeded';
						break;
					case JSON_ERROR_STATE_MISMATCH:
						$msg = ' - Underflow or the modes mismatch';
						break;
					case JSON_ERROR_CTRL_CHAR:
						$msg = ' - Unexpected control character found';
						break;
					case JSON_ERROR_SYNTAX:
						$msg = ' - Syntax error, malformed JSON';
						break;
					case JSON_ERROR_UTF8:
						$msg = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
						break;
					default:
						$msg = ' - Unknown error';
						break;
				}
				throw new Exception( $msg );
			}
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

	return $obj;
}

/**
 * Test whether a URL is valid.
 *
 * @param string $url URL.
 *
 * @return URL if passes, false otherwise.
 */
function wpt_is_valid_url( $url ) {
	if ( is_string( $url ) ) {
		$url = urldecode( $url );

		return preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url );
	} else {
		return false;
	}
}

/**
 * Fetch a remote page. Input url, return content
 *
 * @param string $url URL.
 * @param string $method Method.
 * @param string $body Body of query.
 * @param string $headers Headers to add.
 * @param string $return_type Array key from fetched object to return.
 *
 * @return string|false value from query.
 */
function wpt_fetch_url( $url, $method = 'GET', $body = '', $headers = '', $return_type = 'body' ) {
	$request = new WP_Http();
	$result  = $request->request(
		$url,
		array(
			'method'     => $method,
			'body'       => $body,
			'headers'    => $headers,
			'user-agent' => 'XPoster/https://xposterpro.com',
		)
	);

	if ( ! is_wp_error( $result ) && isset( $result['body'] ) ) {
		if ( 200 === absint( $result['response']['code'] ) ) {
			if ( 'body' === $return_type ) {
				return $result['body'];
			} else {
				return $result;
			}
		} else {
			return $result['body'];
		}
		// Failure (server problem...).
	} else {
		return false;
	}
}

if ( ! function_exists( 'mb_substr_split_unicode' ) ) {
	/**
	 * Fall back function for mb_substr_split_unicode if doesn't exist.
	 *
	 * @param string $str String.
	 * @param int    $split_pos Position to split on.
	 *
	 * @return split output.
	 */
	function mb_substr_split_unicode( $str, $split_pos ) {
		if ( 0 === $split_pos ) {
			return 0;
		}
		$byte_len = strlen( $str );

		if ( $split_pos > 0 ) {
			if ( $split_pos > 256 ) {
				// Optimize large string offsets by skipping ahead N bytes.
				// This will cut out most of our slow time on Latin-based text,
				// and 1/2 to 1/3 on East European and Asian scripts.
				$byte_pos = $split_pos;
				while ( $byte_pos < $byte_len && $str[ $byte_pos ] >= "\x80" && $str[ $byte_pos ] < "\xc0" ) {
					++$byte_pos;
				}
				$char_pos = mb_strlen( substr( $str, 0, $byte_pos ) );
			} else {
				$char_pos = 0;
				$byte_pos = 0;
			}

			while ( $char_pos++ < $split_pos ) {
				++$byte_pos;
				// Move past any tail bytes.
				while ( $byte_pos < $byte_len && $str[ $byte_pos ] >= "\x80" && $str[ $byte_pos ] < "\xc0" ) {
					++$byte_pos;
				}
			}
		} else {
			$split_posx = $split_pos + 1;
			$char_pos   = 0; // relative to end of string; we don't care about the actual char position here.
			$byte_pos   = $byte_len;
			while ( $byte_pos > 0 && $char_pos-- >= $split_posx ) {
				--$byte_pos;
				// Move past any tail bytes.
				while ( $byte_pos > 0 && $str[ $byte_pos ] >= "\x80" && $str[ $byte_pos ] < "\xc0" ) {
					--$byte_pos;
				}
			}
		}

		return $byte_pos;
	}
}

if ( ! function_exists( 'mb_strrpos' ) ) {
	/**
	 * Fallback implementation of mb_strrpos, hardcoded to UTF-8.
	 *
	 * @param string $haystack String.
	 * @param string $needle String.
	 * @param int    $offset integer: optional start position.
	 *
	 * @return int
	 */
	function mb_strrpos( $haystack, $needle, $offset = 0 ) {
		$needle = preg_quote( $needle, '/' );

		$ar = array();
		preg_match_all( '/' . $needle . '/u', $haystack, $ar, PREG_OFFSET_CAPTURE, $offset );

		if ( isset( $ar[0] ) && count( $ar[0] ) > 0 && isset( $ar[0][ count( $ar[0] ) - 1 ][1] ) ) {
			return $ar[0][ count( $ar[0] ) - 1 ][1];
		} else {
			return false;
		}
	}
}

/**
 * This function is obsolete; only exists for people using out of date versions of XPoster Pro.
 *
 * @param string $field Field to check.
 * @param string $value Value to check.
 * @param string $type Type of field.
 *
 * @return checked string.
 */
function wtt_option_selected( $field, $value, $type = 'checkbox' ) {
	switch ( $type ) {
		case 'radio':
		case 'checkbox':
			$result = ' checked="checked"';
			break;
		case 'option':
			$result = ' selected="selected"';
			break;
		default:
			$result = ' selected="selected"';
	}
	if ( $field === $value ) {
		$output = $result;
	} else {
		$output = '';
	}

	return $output;
}

/**
 * Compares two dates to identify which is earlier. Used to differentiate between post edits and original publication.
 *
 * @param string $modified Date this post was modified.
 * @param string $postdate Date this post was published.
 *
 * @return integer (boolean)
 */
function wpt_post_is_new( $modified, $postdate ) {
	/**
	 * Filter the sensitivity used to distinguish between new posts and edits.
	 * Default allows up to a 10 second discrepancy in time stamps where post will be treated as new.
	 * This is necessary because the post date and modified date can sometimes different by a second on any server.
	 *
	 * @hook wpt_edit_sensitivity
	 *
	 * @param {int} $sensitivity Integer representing seconds. Default 10.
	 *
	 * @return {int}
	 */
	$modifier  = apply_filters( 'wpt_edit_sensitivity', 10 ); // alter time in seconds to modified date.
	$mod_date  = strtotime( $modified );
	$post_date = strtotime( $postdate ) + $modifier;
	if ( $mod_date <= $post_date ) { // if post_modified is before or equal to post_date.
		return 1;
	} else {
		return 0;
	}
}

/**
 * Gets the attachment intended for use in status updates for a post.
 *
 * @param integer $post_ID The post ID.
 *
 * @return mixed boolean|integer Attachment ID.
 */
function wpt_post_attachment( $post_ID ) {
	$attachment_id = false;
	/**
	 * Filter whether a post should use its featured image to post with a status update.
	 *
	 * @hook wpt_use_featured_image
	 *
	 * @param {bool} $use True to use the featured image.
	 * @param {int}  $post_ID Post ID.
	 *
	 * @return {bool}
	 */
	$use_featured_image = apply_filters( 'wpt_use_featured_image', true, $post_ID );
	if ( has_post_thumbnail( $post_ID ) && $use_featured_image ) {
		$attachment = get_post_thumbnail_id( $post_ID );
		// X.com & Bluesky API endpoints do not accept GIFs.
		if ( wp_attachment_is( 'gif', $attachment ) ) {
			return false;
		}

		$attachment_id = $attachment;
	} else {
		$args        = array(
			'post_type'      => 'attachment',
			'numberposts'    => 1,
			'post_status'    => 'any',
			'post_parent'    => $post_ID,
			'post_mime_type' => 'image',
			'order'          => 'ASC',
		);
		$attachments = get_posts( $args );
		if ( $attachments ) {
			$attachment_id = $attachments[0]->ID; // Return the first attachment.
		} else {
			$attachment_id = false;
		}
	}
	/**
	 * Filter the attachment ID to post with a status update.
	 *
	 * @hook wpt_post_attachment
	 *
	 * @param {int} $attachment_id Attachment ID.
	 * @param {int} $post_ID Post ID.
	 *
	 * @return {int|bool}
	 */
	$attachment_id = apply_filters( 'wpt_post_attachment', $attachment_id, $post_ID );
	$meta          = wp_get_attachment_metadata( $attachment_id );
	if ( ! isset( $meta['width'], $meta['height'] ) ) {
		wpt_mail( "Image Data Does not Exist for #$attachment_id", wpt_format_error( $meta ), $post_ID );
		$attachment_id = false;
	}
	if ( $attachment_id ) {
		wpt_mail( 'Post has media to upload', "Attachment ID: $attachment_id", $post_ID );
	}

	return $attachment_id;
}

/**
 * Check for a valid license key.
 *
 * @return bool|string
 */
function wpt_pro_is_valid() {
	$license  = ( get_option( 'wpt_license_key' ) ) ? get_option( 'wpt_license_key' ) : 'none';
	$validity = get_option( 'wpt_license_valid' );
	$valid    = false;
	if ( function_exists( 'wpt_pro_functions' ) ) {
		if ( 'none' !== $license ) {
			$valid = ( ( 'true' === $validity ) || ( 'active' === $validity ) || ( 'valid' === $validity ) ) ? $license : false;
		} else {
			$valid = false;
		}
	}

	return $valid;
}

/**
 * Show support form. Note: text in the message body should not be translatable.
 */
function wpt_get_support_form() {
	global $current_user;
	$current_user   = wp_get_current_user();
	$request        = '';
	$response_email = $current_user->user_email;
	// send fields for XPoster.
	$license = wpt_pro_is_valid();
	if ( $license ) {
		$license_key          = 'License Key: ' . $license;
		$version              = XPOSTER_VERSION;
		$wtt_twitter_username = get_option( 'wtt_twitter_username' );
		// send fields for all plugins.
		$wp_version = get_bloginfo( 'version' );
		$home_url   = home_url();
		$wp_url     = site_url();
		$language   = get_bloginfo( 'language' );
		$charset    = get_bloginfo( 'charset' );
		// server.
		$php_version = phpversion();

		// theme data.
		$theme         = wp_get_theme();
		$theme_name    = $theme->get( 'Name' );
		$theme_uri     = $theme->get( 'ThemeURI' );
		$theme_parent  = $theme->get( 'Template' );
		$theme_parent  = ( $theme_parent ) ? $theme_parent : __( 'None', 'wp-to-twitter' );
		$theme_version = $theme->get( 'Version' );

		$admin_email = get_option( 'admin_email' );
		// plugin data.
		$plugins        = get_plugins();
		$plugins_string = '';
		foreach ( array_keys( $plugins ) as $key ) {
			if ( is_plugin_active( $key ) ) {
				$plugin          =& $plugins[ $key ];
				$plugin_name     = $plugin['Name'];
				$plugin_uri      = $plugin['PluginURI'];
				$plugin_version  = $plugin['Version'];
				$plugins_string .= "$plugin_name: $plugin_version; $plugin_uri\n";
			}
		}
		$server = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : 'N/A';
		$agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'N/A';

		$data = "
	================ Installation Data ====================
	==XPoster==
	Version: $version
	X.com username: http://twitter.com/$wtt_twitter_username
	$license_key

	==WordPress:==
	Version: $wp_version
	URL: $home_url
	Install: $wp_url
	Language: $language
	Charset: $charset
	User Email: $current_user->user_email
	Admin Email: $admin_email

	==Extra info:==
	PHP Version: $php_version
	Server Software: $server
	User Agent: $agent

	==Theme:==
	Name: $theme_name
	URI: $theme_uri
	Parent: $theme_parent
	Version: $theme_version

	==Active Plugins:==
	$plugins_string
	";
		if ( isset( $_POST['wpt_support'] ) ) {
			$nonce = ( isset( $_REQUEST['_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : false;
			if ( ! wp_verify_nonce( $nonce, 'wp-to-twitter-nonce' ) ) {
				wp_die( 'XPoster: Security check failed' );
			}
			$request = ( ! empty( $_POST['support_request'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['support_request'] ) ) : false;
			if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
				$pro = ' PRO';
			} else {
				$pro = '';
			}
			$subject        = "XPoster $pro support request.";
			$message        = $request . "\n\n" . $data;
			$response_email = ( isset( $_POST['response_email'] ) ) ? sanitize_email( wp_unslash( $_POST['response_email'] ) ) : false;
			$from           = "From: $current_user->display_name <$response_email>\r\nReply-to: $current_user->display_name <$response_email>\r\n";

			if ( ! $response_email ) {
				wp_admin_notice(
					__( 'Please supply a valid email where you can receive support responses.', 'wp-to-twitter' ),
					array(
						'type' => 'error',
					)
				);
			} elseif ( ! $request ) {
				wp_admin_notice(
					__( 'Please describe your problem. Thank you!', 'wp-to-twitter' ),
					array(
						'type' => 'error',
					)
				);
			} else {
				$sent = wp_mail( 'plugins@xposterpro.com', $subject, $message, $from );
				if ( $sent ) {
					// Translators: Email address.
					$message = sprintf( __( 'Thank you! I\'ll get back to you as soon as I can. Please make sure you can receive email at <code>%s</code>.', 'wp-to-twitter' ), $response_email );
					wp_admin_notice(
						$message,
						array(
							'type' => 'success',
						)
					);
				} else {
					// Translators: URL to plugin support form.
					$message = '<p>' . sprintf( __( "Sorry! I couldn't send that message. Here's the text of your request:", 'wp-to-twitter' ) ) . '</p><p>' . sprintf( __( '<a href="%s">Contact me here</a>, instead.', 'wp-to-twitter' ), 'https://www.xposterpro.com/contact/get-support/' ) . "</p><pre>$request</pre>";
					wp_admin_notice(
						$message,
						array(
							'type'           => 'error',
							'paragraph_wrap' => false,
						)
					);
				}
			}
		}
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		$admin_url = add_query_arg( 'tab', 'support', $admin_url );
		?>
		<form method='post' action='<?php echo esc_url( $admin_url ); ?>'>
			<div><input type='hidden' name='_wpnonce' value='<?php echo esc_attr( wp_create_nonce( 'wp-to-twitter-nonce' ) ); ?>' /></div>
			<div>
			<p><?php esc_html_e( "If you're having trouble with XPoster Pro, please try to answer these questions in your message:", 'wp-to-twitter' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'What were you doing when the problem occurred?', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'What did you expect to happen?', 'wp-to-twitter' ); ?></li>
				<li><?php esc_html_e( 'What happened instead?', 'wp-to-twitter' ); ?></li>
			</ul>
			<p>
			<label for='response_email'><?php esc_html_e( 'Your Email', 'wp-to-twitter' ); ?></label><br />
			<input type='email' name='response_email' id='response_email' value='<?php esc_attr( $response_email ); ?>' class='widefat' required='required' />
			</p>
			<p>
			<label for='support_request'><?php esc_html_e( 'Support Request:', 'wp-to-twitter' ); ?></label><br />
			<textarea class='support-request' name='support_request' id='support_request' cols='80' rows='10' class='widefat'><?php echo esc_textarea( stripslashes( $request ) ); ?></textarea>
			</p>
			<p>
			<input type='submit' value='<?php esc_attr_e( 'Send Support Request', 'wp-to-twitter' ); ?>' name='wpt_support' class='button-primary' />
			</p>
			<p><?php esc_html_e( 'The following additional information will be sent with your support request:', 'wp-to-twitter' ); ?></p>
			</div>
		</form>
		<div class='wpt_support'>
			<?php
			echo wp_kses_post( wpautop( $data ) );
			?>
		</div>
		<?php
	} else {
		?>
		<p>
			<?php
			// translators: 1) link, 2) close link.
			echo sprintf( esc_html__( 'You need a valid XPoster Pro license to receive support. Return to this screen to use the premium support form after %1$sgetting your Pro license%2$s.', 'wp-to-twitter' ), '<a href="https://xposterpro.com/awesome/xposter-pro/">', '</a>' );
			?>
		</p>
		<?php
	}
}

/**
 * Check whether an image has a valid mime type for a service.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $service Service name (lowercase).
 *
 * @return bool
 */
function wpt_check_mime_type( $attachment_id, $service ) {
	$return           = false;
	$valid_mime_types = array(
		'x'        => array(
			'image/jpeg',
			'image/png',
			'image/gif',
			'image/webp',
		),
		'bluesky'  => array(
			'image/jpeg',
			'image/png',
			'image/webp',
			'image/heic',
		),
		'mastodon' => array(
			'image/jpeg',
			'image/png',
			'image/webp',
			'image/heic',
			'image/avif',
			'image/gif',
		),
	);

	$allowed   = $valid_mime_types[ $service ];
	$mime_type = get_post_mime_type( $attachment_id );
	if ( in_array( $mime_type, $allowed, true ) ) {
		$return = true;
	}

	return $return;
}

add_action( 'dp_duplicate_post', 'wpt_delete_copied_meta', 10, 2 );
add_action( 'dp_duplicate_page', 'wpt_delete_copied_meta', 10, 2 );
/**
 * Prevent 'Duplicate Posts' plug-in from copying XPoster meta data
 *
 * @param int    $new_id New post ID.
 * @param object $post Old Post.
 */
function wpt_delete_copied_meta( $new_id, $post ) {
	/**
	 * Filter to allow Duplicate Posts plugin to copy XPoster meta data when a post is duplicated.
	 *
	 * @hook wpt_allow_copy_meta
	 *
	 * @param {bool} $disable True to allow meta to be copied. Default false.
	 *
	 * @return {bool}
	 */
	$disable = apply_filters( 'wpt_allow_copy_meta', false );
	if ( $disable ) {
		return;
	}
	// delete XPoster's meta data from copied post.
	// I can't prevent them from being copied, but I can delete them after the fact.
	delete_post_meta( $new_id, '_wpt_short_url' );
	delete_post_meta( $new_id, '_wp_jd_target' );
	delete_post_meta( $new_id, '_jd_wp_twitter' );
	delete_post_meta( $new_id, '_jd_twitter' );
	delete_post_meta( $new_id, '_wpt_failed' );
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

/**
 * Format errors for storing in debugging data. Recursive.
 *
 * @param array|object $data Data to format.
 *
 * @return string String version of data.
 */
function wpt_format_error( $data ) {
	if ( ! is_array( $data ) ) {
		if ( is_object( $data ) ) {
			$data = json_decode( wp_json_encode( $data ), true );
		} else {
			$data = (array) $data;
		}
	}
	$output = '';
	foreach ( $data as $key => $value ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			$output .= '<li><strong>' . esc_html( $key ) . '</strong>:' . wpt_format_error( $value );
		} else {
			$output .= '<li><strong>' . esc_html( $key ) . '</strong>:' . esc_html( $value ) . PHP_EOL . '</li>';
		}
	}

	return '<ul>' . $output . '</ul>';
}

/**
 * Adds links, mentions, and hashtags to status updates.
 *
 * @param string $text A string representing the content of a status update.
 * @param string $service Service being parsed for. Default Bluesky.
 *
 * @return string Linkified tweet content
 */
function wpt_text_linkify( $text, $service = 'bluesky' ) {
	switch ( $service ) {
		case 'bluesky':
			$profile_url = 'https://bsky.app/profile';
			$hash_url    = 'https://bsky.app/hashtag/';
			break;
		case 'mastodon':
			$profile_url = '' . '/@';
			$hash_url    = '' . '/tags/';
			break;
		case 'x':
			$profile_url = 'https://x.com';
			$hash_url    = 'https://x.com/search?q=%23';
			break;
		default:
			$profile_url = 'https://bsky.app/profile';
			$hash_url    = 'https://bsky.app/hashtag/';
	}

	$text = html_entity_decode( make_clickable( $text ) );
	$text = preg_replace( '/@(\w.+)/', '<a href="' . $profile_url . '/\\1" rel="nofollow">@\\1</a>', $text );
	$text = preg_replace( '/#(\w+)/', '<a href="' . $hash_url . '\\1" rel="nofollow">#\\1</a>', $text );

	return $text;
}
