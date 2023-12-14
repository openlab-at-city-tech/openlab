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

include( dirname( __FILE__ ) . '/classes/class-wpt-normalizer.php' );

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
			return 'checked="checked"';
		} else {
			return '';
		}
	}
	if ( 1 === (int) get_option( $field ) ) {
		return 'checked="checked"';
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
 * Insert a Tweet record into logs.
 *
 * @param string $data Option key.
 * @param int    $id Post ID.
 * @param string $message Log message.
 */
function wpt_set_log( $data, $id, $message ) {
	if ( 'test' === $id ) {
		update_option( $data, $message );
	} else {
		update_post_meta( $id, '_' . $data, $message );
	}
	update_option( $data . '_last', array( $id, $message ) );
}

/**
 * Get information from Tweet logs.
 *
 * @param string $data Option key.
 * @param int    $id Post ID.
 *
 * @return string message.
 */
function wpt_get_log( $data, $id ) {
	if ( 'test' === $id ) {
		$log = get_option( $data );
	} elseif ( 'last' === $id ) {
		$log = get_option( $data . '_last' );
	} else {
		$log = get_post_meta( $id, '_' . $data, true );
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
	$shrink   = apply_filters( 'wptt_shorten_link', $testurl, $title, false, true );
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
	// check twitter credentials.
	if ( wtt_oauth_test() ) {
		$rand     = wp_rand( 1000000, 9999999 );
		$testpost = wpt_post_to_twitter( "This is a test of XPoster. $shrink ($rand)" );
		if ( $testpost ) {
			$message .= '<li><strong>' . __( 'XPoster successfully submitted a status update to X.com.', 'wp-to-twitter' ) . '</strong></li>';
		} else {
			$error    = wpt_get_log( 'wpt_status_message', 'test' );
			$message .= '<li class="error"><strong>' . __( 'XPoster failed to submit an update to X.com.', 'wp-to-twitter' ) . '</strong></li>';
			$message .= "<li class='error'>$error</li>";
		}
	} else {
		$message .= '<strong>' . __( 'You have not connected WordPress to X.com.', 'wp-to-twitter' ) . '</strong> ';
	}
	if ( false === $testpost && false === $shrink ) {
		$message .= '<li class="error">' . __( "<strong>Your server does not appear to support the required methods for XPoster to function.</strong> You can try it anyway - these tests aren't perfect.", 'wp-to-twitter' ) . '</li>';
	}
	if ( $testpost && $shrink ) {
		$message .= '<li><strong>' . __( 'Your server should run XPoster successfully.', 'wp-to-twitter' ) . '</strong></li>';
	}
	$message .= '</ul>
	</div>';

	return $message;
}

/**
 * Generate Settings links.
 */
function wpt_settings_tabs() {
	$output   = '';
	$username = get_option( 'wtt_twitter_username' );
	$default  = ( '' === $username || false === $username ) ? 'connection' : 'basic';
	$current  = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( $_GET['tab'] ) : $default;
	$pro_text = ( function_exists( 'wpt_pro_exists' ) ) ? __( 'Pro Settings', 'wp-to-twitter' ) : __( 'XPoster PRO', 'wp-to-twitter' );
	$pages    = array(
		'connection' => __( 'X Connection', 'wp-to-twitter' ),
		'basic'      => __( 'Basic Settings', 'wp-to-twitter' ),
		'shortener'  => __( 'URL Shortener', 'wp-to-twitter' ),
		'advanced'   => __( 'Advanced Settings', 'wp-to-twitter' ),
		'support'    => __( 'Get Help', 'wp-to-twitter' ),
		'pro'        => $pro_text,
	);

	$pages     = apply_filters( 'wpt_settings_tabs_pages', $pages, $current );
	$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );

	foreach ( $pages as $key => $value ) {
		$selected = ( $key === $current ) ? ' nav-tab-active' : '';
		$url      = esc_url( add_query_arg( 'tab', $key, $admin_url ) );
		if ( 'pro' === $key ) {
			$output .= "<a class='wpt-pro-tab nav-tab$selected' href='$url'>$value</a>";
		} else {
			$output .= "<a class='nav-tab$selected' href='$url'>$value</a>";
		}
	}
	echo $output;
}

/**
 * Show the last Tweet attempt as admin notice.
 */
function wpt_show_last_tweet() {
	if ( apply_filters( 'wpt_show_last_tweet', true ) ) {
		$log = wpt_get_log( 'wpt_status_message', 'last' );
		if ( ! empty( $log ) && is_array( $log ) ) {
			$post_ID = $log[0];
			$post    = get_post( $post_ID );
			if ( is_object( $post ) ) {
				$title = "<a href='" . esc_url( get_edit_post_link( $post_ID ) ) . "'>" . esc_html( $post->post_title ) . '</a>';
			} else {
				$title = '(' . __( 'No post', 'wp-to-twitter' ) . ')';
			}
			$notice = esc_html( $log[1] );
			echo "<div class='updated'><p><strong>" . __( 'Last Tweet', 'wp-to-twitter' ) . "</strong>: $title &raquo; $notice</p></div>";
		}
	}
}

/**
 * Handle Tweet & URL shortener errors.
 */
function wpt_handle_errors() {
	if ( isset( $_POST['submit-type'] ) && 'clear-error' === $_POST['submit-type'] ) {
		delete_option( 'wp_url_failure' );
	}
	if ( '1' === get_option( 'wp_url_failure' ) ) {
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		$nonce     = wp_nonce_field( 'wp-to-twitter-nonce', '_wpnonce', true, false ) . wp_referer_field( false );
		$error     = '<div class="error"><p>' . __( 'The query to the URL shortener API failed, and your URL was not shrunk. The full post URL was attached to your Tweet. Check with your URL shortening provider to see if there are any known issues.', 'wp-to-twitter' ) .
			'</p><form method="post" action="' . $admin_url . '">
				<div>
					<input type="hidden" name="submit-type" value="clear-error"/>
					' . $nonce . '
				</div>
				<p>
					<input type="submit" name="submit" value="' . __( 'Clear Error Messages', 'wp-to-twitter' ) . '" class="button-primary" />
				</p>
			</form>
		</div>';

		echo $error;
	}
}

/**
 * Verify user capabilities
 *
 * @param string $role Role name.
 * @param string $cap Capability name.
 *
 * @return Check if has capability.
 */
function wpt_check_caps( $role, $cap ) {
	$role = get_role( $role );
	if ( $role->has_cap( $cap ) ) {
		return " checked='checked'";
	}
	return '';
}

/**
 * Output checkbox for user capabilities
 *
 * @param string $role Role name.
 * @param string $cap Capability name.
 * @param string $name Display name for capability.
 *
 * @return Checkbox HTML.
 */
function wpt_cap_checkbox( $role, $cap, $name ) {
	return "<li><input type='checkbox' id='wpt_caps_{$role}_$cap' name='wpt_caps[$role][$cap]' value='on'" . wpt_check_caps( $role, $cap ) . " /> <label for='wpt_caps_{$role}_$cap'>" . esc_html( $name ) . '</label></li>';
}

/**
 * Send a debug message. (Used email by default until 3.3.)
 *
 * @param string  $subject Subject of error.
 * @param string  $body Body of error.
 * @param int     $post_ID ID of Post being Tweeted.
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
 * @param int    $post_ID ID of post being Tweeted.
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
		if ( is_array( $debug_log ) ) {
			foreach ( $debug_log as $entry ) {
				$date     = date_i18n( 'Y-m-d H:i:s', $entry[0] );
				$subject  = $entry[1];
				$body     = $entry[2];
				$records .= "<li><button type='button' class='toggle-debug button-secondary' aria-expanded='false'><strong>$date</strong>:<br />" . esc_html( $subject ) . "</button><pre class='wpt-debug-details'>" . esc_html( $body ) . '</pre></li>';
			}
		}
		$script = "
<script>
(function ($) {
	$(function() {
		$( 'button.toggle-debug' ).on( 'click', function() {
			var next = $( this ).next( 'pre' );
			if ( $( this ).next( 'pre' ).is( ':visible' ) ) {
				$( this ).next( 'pre' ).hide();
				$( this ).attr( 'aria-expanded', 'false' );
			} else {
				$( this ).next( 'pre' ).show();
				$( this ).attr( 'aria-expanded', 'true' );
			}
		});
	})
})(jQuery);
</script>";
		$delete = "<ul>
		<li><input type='checkbox' name='wpt-delete-debug' value='true' id='wpt-delete-debug'> <label for='wpt-delete-debug'>" . __( 'Delete debugging logs on this post', 'wp-to-twitter' ) . "</label></li>
		<li><input type='checkbox' name='wpt-delete-all-debug' value='true' id='wpt-delete-all-debug'> <label for='wpt-delete-all-debug'>" . __( 'Delete debugging logs for all posts', 'wp-to-twitter' ) . '</label></li>
		</ul>';

		echo ( '' !== $records ) ? "$script<div class='wpt-debug-log'><h3>Debugging Log:</h3><ul>$records</ul></div>$delete" : '';
	}
}

/**
 * Send a remote query expecting JSON.
 *
 * @param string $url Target URL.
 * @param array  $array Arguments if not default.
 * @param string $method Query method.
 * @throws Exception JSON error string.
 *
 * @return JSON object.
 */
function wpt_remote_json( $url, $array = true, $method = 'GET' ) {
	$input = wpt_fetch_url( $url, $method );
	$obj   = json_decode( $input, $array );
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
 * @param string $return Array key from fetched object to return.
 *
 * @return string|false value from query.
 */
function wpt_fetch_url( $url, $method = 'GET', $body = '', $headers = '', $return = 'body' ) {
	$request = new WP_Http;
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
			if ( 'body' === $return ) {
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
	// Default allows up to a 10 second discrepancy for slow processing.
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
 * Gets the first attachment for the supplied post.
 *
 * @param integer $post_ID The post ID.
 *
 * @return mixed boolean|integer Attachment ID.
 */
function wpt_post_attachment( $post_ID ) {
	$return             = false;
	$use_featured_image = apply_filters( 'wpt_use_featured_image', true, $post_ID );
	if ( has_post_thumbnail( $post_ID ) && $use_featured_image ) {
		$attachment = get_post_thumbnail_id( $post_ID );
		// X.com API endpoint does not accept GIFs.
		if ( wp_attachment_is( 'gif', $attachment ) ) {
			return false;
		}

		$return = $attachment;
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
			$return = $attachments[0]->ID; // Return the first attachment.
		} else {
			$return = false;
		}
	}

	return apply_filters( 'wpt_post_attachment', $return, $post_ID );
}

/**
 * Show support form. Note: text in the message body should not be translatable.
 */
function wpt_get_support_form() {
	global $current_user, $wpt_version;
	$current_user   = wp_get_current_user();
	$request        = '';
	$response_email = $current_user->user_email;
	// send fields for XPoster.
	$license = ( get_option( 'wpt_license_key' ) ) ? get_option( 'wpt_license_key' ) : 'none';
	if ( 'none' !== $license ) {
		$valid = ( ( 'true' === get_option( 'wpt_license_valid' ) ) || ( 'active' === get_option( 'wpt_license_valid' ) ) || ( 'valid' === get_option( 'wpt_license_valid' ) ) ) ? ' (active)' : ' (inactive)';
	} else {
		$valid = '';
	}
	if ( $valid && function_exists( 'wpt_pro_functions' ) ) {
		$license = 'License Key: ' . $license . $valid;

		$version              = $wpt_version;
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

		$data = "
	================ Installation Data ====================
	==XPoster==
	Version: $version
	X.com username: http://twitter.com/$wtt_twitter_username
	$license

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
	Server Software: $_SERVER[SERVER_SOFTWARE]
	User Agent: $_SERVER[HTTP_USER_AGENT]

	==Theme:==
	Name: $theme_name
	URI: $theme_uri
	Parent: $theme_parent
	Version: $theme_version

	==Active Plugins:==
	$plugins_string
	";
		if ( isset( $_POST['wpt_support'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $nonce, 'wp-to-twitter-nonce' ) ) {
				wp_die( 'XPoster: Security check failed' );
			}
			$request = ( ! empty( $_POST['support_request'] ) ) ? stripslashes( sanitize_textarea_field( $_POST['support_request'] ) ) : false;
			if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
				$pro = ' PRO';
			} else {
				$pro = '';
			}
			$subject        = "XPoster $pro support request.";
			$message        = $request . "\n\n" . $data;
			$response_email = ( isset( $_POST['response_email'] ) ) ? sanitize_email( $_POST['response_email'] ) : false;
			$from           = "From: $current_user->display_name <$response_email>\r\nReply-to: $current_user->display_name <$response_email>\r\n";

			if ( ! $response_email ) {
				echo "<div class='notice error'><p>" . __( 'Please supply a valid email where you can receive support responses.', 'wp-to-twitter' ) . '</p></div>';
			} elseif ( ! $request ) {
				echo "<div class='notice error'><p>" . __( 'Please describe your problem. I\'m not psychic.', 'wp-to-twitter' ) . '</p></div>';
			} else {
				$sent = wp_mail( 'plugins@xposterpro.com', $subject, $message, $from );
				if ( $sent ) {
					// Translators: Email address.
					echo "<div class='notice updated'><p>" . sprintf( __( 'Thank you for supporting XPoster! I\'ll get back to you as soon as I can. Please make sure you can receive email at <code>%s</code>.', 'wp-to-twitter' ), $response_email ) . '</p></div>';
				} else {
					// Translators: URL to plugin support form.
					echo "<div class='notice error'><p>" . __( "Sorry! I couldn't send that message. Here's the text of your request:", 'wp-to-twitter' ) . '</p><p>' . sprintf( __( '<a href="%s">Contact me here</a>, instead.', 'wp-to-twitter' ), 'https://www.joedolson.com/contact/get-support/' ) . "</p><pre>$request</pre></div>";
				}
			}
		}
		$admin_url = admin_url( 'admin.php?page=wp-tweets-pro' );
		$admin_url = add_query_arg( 'tab', 'support', $admin_url );
		echo "
		<form method='post' action='$admin_url'>
			<div><input type='hidden' name='_wpnonce' value='" . wp_create_nonce( 'wp-to-twitter-nonce' ) . "' /></div>
			<div>
			<p>" . __( "If you're having trouble with XPoster Pro, please try to answer these questions in your message:", 'wp-to-twitter' ) . '</p>
			<ul>
				<li>' . __( 'What were you doing when the problem occurred?', 'wp-to-twitter' ) . '</li>
				<li>' . __( 'What did you expect to happen?', 'wp-to-twitter' ) . '</li>
				<li>' . __( 'What happened instead?', 'wp-to-twitter' ) . "</li>
			</ul>
			<p>
			<label for='response_email'>" . __( 'Your Email', 'wp-to-twitter' ) . "</label><br />
			<input type='email' name='response_email' id='response_email' value='$response_email' class='widefat' required='required' aria-required='true' />
			</p>
			<p>
			<label for='support_request'>" . __( 'Support Request:', 'wp-to-twitter' ) . "</label><br /><textarea class='support-request' name='support_request' id='support_request' cols='80' rows='10' class='widefat'>" . stripslashes( esc_attr( $request ) ) . "</textarea>
			</p>
			<p>
			<input type='submit' value='" . __( 'Send Support Request', 'wp-to-twitter' ) . "' name='wpt_support' class='button-primary' />
			</p>
			<p>" .
			__( 'The following additional information will be sent with your support request:', 'wp-to-twitter' )
			. '</p>
			</div>
		</form>';

		echo "<div class='wpt_support'>
		" . wpautop( $data ) . '
		</div>';
	} else {
		echo '<p>' . __( 'You need a valid XPoster Pro license to receive support. Return to this screen to use the premium support form after <a href="https://xposterpro.com/awesome/xposter-pro/">getting your Pro license</a>.', 'wp-to-twitter' ) . '</p>';
	}
	wpt_faq();
}

/**
 * FAQ questions.
 */
function wpt_faq() {
	$qs = array(
		array(
			'question' => __( 'My app has been suspended by X.com. What do I do now?', 'wp-to-twitter' ),
			'answer'   => __( 'Some users have been successful by removing their existing app and creating a new one, following the setup instructions in the X.com Connection tab. It is unlikely you will make any progress by contesting the suspension.', 'wp-to-twitter' ),
		),
		array(
			'question' => __( "I'm receiving a '401 Unauthorized' error from X.com, but my credentials haven't changed. What should I do?", 'wp-to-twitter' ),
			'answer'   => __( 'First, check and see whether your app has been suspended in your X.com developer account. If it has, see above. If not, this is most likely a temporary problem in the X.com API; but you can try generating new keys and secrets in your developer account and re-connect your app. Some users have also been successful by changing their account status to the free account. (Older accounts may have a legacy status that is not handled well by X.com.)', 'wp-to-twitter' ),
		),
	);

	echo '<h2>' . __( 'Frequently Asked Questions', 'wp-to-twitter' ) . '</h2>';
	echo '<p>' . __( '<strong>Please note:</strong> These answers are mostly guesswork; user experiences have been inconsistent and the documentation does not always match real behavior.', 'wp-to-twitter' ) . '</p>';
	foreach ( $qs as $q ) {
		echo '<h3>' . $q['question'] . '</h3>';
		echo wpautop( $q['answer'] );
	}
}

/**
 * Check whether a file is writable.
 *
 * @param string $file Filename/path.
 *
 * @return boolean.
 */
function wpt_is_writable( $file ) {
	if ( function_exists( 'wp_is_writable' ) ) {
		$is_writable = wp_is_writable( $file );
	} else {
		$is_writable = is_writeable( $file );
	}

	return $is_writable;
}

/**
 * Make a curl query.
 *
 * @param string $url URL to query.
 *
 * @return Curl response.
 */
function wp_get_curl( $url ) {

	$curl = curl_init( $url );

	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HEADER, 0 );
	curl_setopt( $curl, CURLOPT_USERAGENT, '' );
	curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );

	$response = curl_exec( $curl );
	if ( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {
		$response = false;
	} // end if.
	curl_close( $curl );

	return $response;
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
 * Provide aliases for changed function names if plug-ins or themes are calling XPoster functions in custom code.
 *
 * @param string $url Query url.
 * @param string $method Method.
 * @param string $body Body.
 * @param string $headers Headers.
 * @param string $return Return data.
 *
 * @return data.
 */
function jd_fetch_url( $url, $method = 'GET', $body = '', $headers = '', $return = 'body' ) {
	return wpt_fetch_url( $url, $method, $body, $headers, $return );
}

/**
 * Alias for remote_json.
 *
 * @param string $url Query url.
 * @param array  $array Arguments.
 *
 * @return remote JSON.
 */
function jd_remote_json( $url, $array = true ) {
	return wpt_remote_json( $url, $array );
}

/**
 * Send a Tweet for a new link.
 *
 * @param int $link_id Link ID.
 *
 * @return twit link.
 */
function jd_twit_link( $link_id ) {
	return wpt_twit_link( $link_id );
}

/**
 * Get post data.
 *
 * @param int $post_ID Post ID.
 *
 * @return Array post data.
 */
function jd_post_info( $post_ID ) {
	return wpt_post_info( $post_ID );
}

/**
 * Sent post tweet.
 *
 * @param int    $post_ID Post ID.
 * @param string $type Type of post.
 *
 * @return tweet
 */
function jd_twit( $post_ID, $type = 'instant' ) {
	return wpt_tweet( $post_ID, $type );
}

/**
 * Update oauth settings.
 *
 * @param mixed boolean/int   $auth Author ID.
 * @param mixed boolean/array $post POST data.
 *
 * @return update.
 */
function jd_update_oauth_settings( $auth = false, $post = false ) {
	return wpt_update_oauth_settings( $auth, $post );
}
