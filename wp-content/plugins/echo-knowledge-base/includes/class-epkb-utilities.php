<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Various utility functions
 */
class EPKB_Utilities {

	static $wp_options_cache = array();
	static $postmeta = array();

	const ADMIN_CAPABILITY = 'manage_options';

	 /**
	 * Get Post status translation.
	 * @param $post_status
	 * @return mixed
	 */
	public static function get_post_status_text( $post_status ) {

		$post_statuses = array( 'draft' => esc_html__( 'Draft' ), 'pending' => esc_html__( 'Pending' ),
		                        'publish' => esc_html__( 'Published' ), 'future' => esc_html__( 'Scheduled' ),
								'private' => esc_html__( 'Private' ),
								'trash'   => esc_html__( 'Trash' ));

		if ( empty( $post_status ) || ! in_array( $post_status, array_keys( $post_statuses ) ) ) {
			return $post_status;
		}

		return $post_statuses[$post_status];
	}

	public static function get_eckb_kb_id( $default=1 ) {
		global $eckb_kb_id;
		return empty( $eckb_kb_id ) ? $default : $eckb_kb_id;
	}

	public static function get_current_term() {
		$term = get_queried_object();
		return empty( $term ) || ! $term instanceof WP_Term ? null : $term;
	}


	/**************************************************************************************************************************
	 *
	 *                     STRING OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * PHP substr() function returns FALSE if the input string is empty. This method
	 * returns empty string if input is empty or if error occurs.
	 *
	 * @param $string
	 * @param $start
	 * @param null $length
	 *
	 * @return string
	 */
	public static function substr( $string, $start, $length=null ) {
		$result = substr( $string, $start, $length );
		return empty( $result ) ? '' : $result;
	}

	/**
	 * Darken a color represented in the format #RRGGBB (hex).
	 *
	 * @param string $color    The original color in #RRGGBB format.
	 * @param float $percent   The percentage by which to darken the color (0 to 1).
	 *
	 * Example: darken_hex_color($originalColor, 0.2); // Darken by 20%
	 *
	 * @return string          The darkened color in #RRGGBB format.
	 */
	public static function darken_hex_color( $color, $percent ) {
		// Convert the hex color to its RGB components
		$r = hexdec( substr( $color, 1, 2 ) );
		$g = hexdec( substr( $color, 3, 2 ) );
		$b = hexdec( substr( $color, 5, 2 ) );

		// Darken the color components
		$r *= ( 1 - $percent );
		$g *= ( 1 - $percent );
		$b *= ( 1 - $percent );

		// Ensure the RGB values stay within the valid range (0-255)
		$r = max( 0, min( 255, $r ) );
		$g = max( 0, min( 255, $g ) );
		$b = max( 0, min( 255, $b ) );

		// Convert the darkened RGB values back to hex format
		return '#' . sprintf( '%02X%02X%02X', $r, $g, $b );
	}

	/**
	 * Lighten a color represented in the format #RRGGBB (hex).
	 *
	 * @param string $color    The original color in #RRGGBB format.
	 * @param float $percent   The percentage by which to lighten the color (0 to 1).
	 *
	 * * Example: lighten_hex_color($originalColor, 0.2); // Lighten by 20%
	 *
	 * @return string          The lightened color in #RRGGBB format.
	 */
	public static function lighten_hex_color($color, $percent) {
		// Convert the hex color to its RGB components
		$r = hexdec( substr( $color, 1, 2 ) );
		$g = hexdec( substr( $color, 3, 2 ) );
		$b = hexdec( substr( $color, 5, 2 ) );

		// Lighten the color components
		$r = min( 255, $r + ( 255 - $r ) * $percent );
		$g = min( 255, $g + ( 255 - $g ) * $percent );
		$b = min( 255, $b + ( 255 - $b ) * $percent );

		// Convert the lightened RGB values back to hex format
		return '#' . sprintf( '%02X%02X%02X', $r, $g, $b );
	}


	/**************************************************************************************************************************
	 *
	 *                     NUMBER OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * Determine if value is positive integer ( > 0 )
	 * @param int $number is checked
	 * @return bool
	 */
	public static function is_positive_int( $number ) {

		// no invalid format
		if ( empty( $number ) || ! is_numeric( $number ) ) {
			return false;
		}

		// no non-digit characters
		$numbers_only = preg_replace('/\D/', "", $number );
		if ( empty( $numbers_only ) || $numbers_only != $number ) {
			return false;
		}

		// only positive
		return $numbers_only > 0;
	}

	/**
	 * Determine if value is positive integer
	 * @param int $number is check
	 * @return bool
	 */
	public static function is_positive_or_zero_int( $number ) {

		if ( ! isset($number) || ! is_numeric($number) ) {
			return false;
		}

		if ( ( (int) $number) != ( (float) $number )) {
			return false;
		}

		$number = (int) $number;

		return is_int($number);
	}


	/**************************************************************************************************************************
	 *
	 *                     DATE OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * Retrieve specific format from given date-time string e.g. '10-16-2003 10:20:01' becomes '10-16-2003'
	 *
	 * @param $datetime_str
	 * @param string $format e.g. 'Y-m-d H:i:s'  or  'M j, Y'
	 *
	 * @return string formatted date or the original string
	 */
	public static function get_formatted_datetime_string( $datetime_str, $format='M j, Y' ) {

		if ( empty($datetime_str) || empty($format) ) {
			return $datetime_str;
		}

		$time = strtotime($datetime_str);
		if ( empty($time) ) {
			return $datetime_str;
		}

		$date_time = date_i18n($format, $time);
		if ( $date_time == $format ) {
			$date_time = $datetime_str;
		}

		return empty($date_time) ? $datetime_str : $date_time;
	}

	/**
	 * Get nof hours passed between two dates.
	 *
	 * @param string $date1
	 * @param string $date2 OR if empty then use current date
	 *
	 * @return int - number of hours between dates [0-x] or null if error
	 */
	public static function get_hours_since( $date1, $date2='' ) {

		try {
			$date1_dt = new DateTime( $date1 );
			$date2_dt = new DateTime( $date2 );
		} catch(Exception $ex) {
			return null;
		}

		if ( empty($date1_dt) || empty($date2_dt) ) {
			return null;
		}

		$hours = date_diff($date1_dt, $date2_dt)->h;

		return $hours === false ? null : $hours;
	}

	/**
	 * Get nof days passed between two dates.
	 *
	 * @param string $date1
	 * @param string $date2 OR if empty then use current date
	 *
	 * @return int - number of days between dates [0-x] or null if error
	 */
	public static function get_days_since( $date1, $date2='' ) {

		try {
			$date1_dt = new DateTime( $date1 );
			$date2_dt = new DateTime( $date2 );
		} catch(Exception $ex) {
			return null;
		}

		if ( empty($date1_dt) || empty($date2_dt) ) {
			return null;
		}

		$days = (int)date_diff($date1_dt, $date2_dt)->format("%r%a");

		return $days === false ? null : $days;
	}

	/**
	 * How long ago pass date occurred.
	 *
	 * @param string $date1
	 *
	 * @return string x year|month|week|day|hour|minute|second(s) or '[unknown]' on error
	 */
	public static function time_since_today( $date1 ) {
		return self::how_long_ago( $date1 );
	}

	/**
	 * How long ago since now.
	 *
	 * @param string $date1
	 * @param string $date2 or if empty use current time
	 *
	 * @return string x year|month|week|day|hour|minute|second(s) or '[unknown]' on error
	 */
	public static function how_long_ago( $date1, $date2='' ) {

		$time1 = strtotime($date1);
		$time2 = empty($date2) ? time() : strtotime($date2);
		if ( empty($time1) || empty($time2) ) {
			return '[???]';
		}

		$time = abs($time2 - $time1);
		$time = ( $time < 1 )? 1 : $time;
		$tokens = array (
			31536000 => esc_html__( 'year' ),
			2592000 => esc_html__( 'month' ),
			604800 => esc_html__( 'week' ),
			86400 => esc_html__( 'day' ),
			3600 => esc_html__( 'hour' ),
			60 => esc_html__( 'min' ),
			1 => esc_html__( 'sec' )
		);

		$output = '';
		foreach ($tokens as $unit => $text) {
			if ($time >= $unit) {
				$numberOfUnits = floor($time / $unit);
				$output =  $numberOfUnits . ' ' . $text . ( $numberOfUnits >1 ? 's' : '');
				break;
			}
		}

		return $output;
	}


	/**************************************************************************************************************************
	 *
	 *                     AJAX NOTICES
	 *
	 *************************************************************************************************************************/

	/**
	 * wp_die with an error message if nonce invalid or user does not have correct permission
	 *
	 * @param string $context - leave empty if only admin can access this
	 */
	public static function ajax_verify_nonce_and_admin_permission_or_error_die( $context='' ) {

		// check wpnonce
		$wp_nonce = self::post( '_wpnonce_epkb_ajax_action' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epkb_ajax_action' ) ) {
			self::ajax_show_error_die( esc_html__( 'Login or refresh this page to edit this knowledge base', 'echo-knowledge-base' ) . ' (E01)'  );
		}

		// without context only admins can make changes
		if ( empty( $context ) ) {
			if ( ! current_user_can( self::ADMIN_CAPABILITY ) ) {
				self::ajax_show_error_die( esc_html__( 'Login or refresh this page', 'echo-knowledge-base' ) . ' (E02)' );
			}
			return;
		}

		// ensure user has correct permission
		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( $context ) ) {
			self::ajax_show_error_die(__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . ' (E02)');
		}
	}

	/**
	 * wp_die with an error message if nonce invalid or user does not have correct permission
	 *
	 * @param string $capability
	 */
	public static function ajax_verify_nonce_and_capability_or_error_die( $capability = false ) {

		// check wpnonce
		$wp_nonce = self::post( '_wpnonce_epkb_ajax_action' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epkb_ajax_action' ) ) {
			self::ajax_show_error_die( esc_html__( 'Login or refresh this page to edit this knowledge base', 'echo-knowledge-base' ) . ' (E03)'  );
		}

		// no needs check permission if capability false or user admin
		if ( $capability === false || current_user_can( self::ADMIN_CAPABILITY ) ) {
			return;
		}

		// ensure user has correct permission
		if ( ! current_user_can( $capability ) ) {
			self::ajax_show_error_die( esc_html__( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . ' (E04)' );
		}
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param string $message
	 * @param string $title
	 * @param string $type
	 */
	public static function ajax_show_info_die( $message='', $title='', $type='success' ) {
		if ( defined('DOING_AJAX') ) {
			wp_die( wp_json_encode( array( 'message' => EPKB_HTML_Forms::notification_box_bottom( $message, $title, $type ) ) ) );
		}
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param $message
	 * @param string $title
	 * @param string $error_code
	 */
	public static function ajax_show_error_die( $message, $title = '', $error_code = '' ) {
		if ( defined('DOING_AJAX') ) {
			wp_die( wp_json_encode( array( 'error' => true, 'message' => EPKB_HTML_Forms::notification_box_bottom( $message, $title, 'error' ), 'error_code' => $error_code ) ) );
		}
	}

	public static function user_not_logged_in() {
		if ( defined('DOING_AJAX') ) {
			self::ajax_show_error_die( '<p>' . esc_html__( 'You are not logged in. Refresh your page and log in.', 'echo-knowledge-base' ) . '</p>', esc_html__( 'Cannot save your changes', 'echo-knowledge-base' ) );
		}
	}

	/**
	 * Common way to show support link
	 * @return string
	 */
	public static function contact_us_for_support() {

		// show only for admins and editors
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include( ABSPATH . "wp-includes/pluggable.php" );
		}

		$user = wp_get_current_user();
		if ( empty( $user ) || empty( $user->roles ) ) {
			return '';
		}

		if ( ! in_array( 'administrator', $user->roles ) ) {
			return '';
		}

		return ' ' . esc_html__( 'Please contact us for help', 'echo-knowledge-base' ) . ' ' .
		       '<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'here', 'echo-knowledge-base' ) . '</a>.';
	}

	/**
	 * Common way to show feedback link
	 * @return string
	 */
	public static function contact_us_for_feedback() {
		return ' ' .  esc_html__( "We'd love to hear your feedback!", 'echo-knowledge-base' ) . ' ' .
		       '<a href="https://www.echoknowledgebase.com/feature-request/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'click here', 'echo-knowledge-base' ) . '</a>';
	}

	/**
	 * Get string for generic error, optional specific error number, and Contact us link
	 *
	 * For example: EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 411 ) );
	 *
	 * @param int $error_number
	 * @param string $message
	 * @param bool $contact_us
	 * @return string
	 */
	public static function report_generic_error( $error_number=0, $message='', $contact_us=true ) {

		if ( empty( $message ) ) {
			$message = esc_html__( 'Error occurred', 'echo-knowledge-base' );
		} else if ( is_wp_error( $message ) ) {
			/** @var WP_Error $message */
			$message = $message->get_error_message();
		} else if ( ! is_string( $message ) ) {
			$message = esc_html__( 'Error occurred', 'echo-knowledge-base' );
		}

		return $message .
				( empty( $error_number ) ? '' : ' (' . $error_number . '). ' ) .
				( empty( $contact_us ) ? '' : self::contact_us_for_support() );
	}


	/**************************************************************************************************************************
	 *
	 *                     SECURITY
	 *
	 *************************************************************************************************************************/

	/**
	 * Return digits only.
	 *
	 * @param $number
	 * @param int $default
	 * @return int|$default
	 */
	public static function sanitize_int( $number, $default=0 ) {

		if ( $number === null || ! is_numeric( $number ) ) {
			return $default;
		}

		// intval returns 0 on error so handle 0 here first
		if ( $number == 0 ) {
			return 0;
		}

		$number = intval( $number );

		return empty( $number ) ? $default : (int) $number;
	}

	/**
	 * Return text, space, "-" and "_" only.
	 *
	 * @param $text
	 * @param String $default
	 * @return String|$default
	 */
	public static function sanitize_english_text( $text, $default='' ) {

		if ( empty($text) || ! is_string($text) ) {
			return $default;
		}

		$text = preg_replace('/[^A-Za-z0-9 \-_]/', '', $text);

		return empty($text) ? $default : $text;
	}

	/**
	 * Retrieve ID or return error. Used for IDs.
	 *
	 * @param mixed $id is either $id number or array with 'id' index
	 *
	 * @return int|WP_Error
	 */
	public static function sanitize_get_id( $id ) {

		if ( empty( $id ) || is_wp_error( $id ) ) {
			EPKB_Logging::add_log( 'Error occurred (01)' );
			return new WP_Error( 'E001', 'invalid ID' );
		}

		if ( is_array( $id ) ) {
			if ( ! isset( $id['id']) ) {
				EPKB_Logging::add_log( 'Error occurred (02)' );
				return new WP_Error('E002', 'invalid ID' );
			}

			$id_value = $id['id'];
			if ( ! self::is_positive_int( $id_value ) ) {
				EPKB_Logging::add_log( 'Error occurred (03)', $id_value );
				return new WP_Error( 'E003', 'invalid ID' );
			}

			return (int) $id_value;
		}

		if ( ! self::is_positive_int( $id ) ) {
			EPKB_Logging::add_log( 'Error occurred (04)', $id );
			return new WP_Error( 'E004', 'invalid ID' );
		}

		return (int) $id;
	}

    /**
     * Sanitize array full of ints.
     *
     * @param $array_values
     * @param string $default
     * @return array|string
     */
	public static function sanitize_int_array( $array_values, $default='' ) {
	    if ( ! is_array($array_values) ) {
	        return $default;
        }

        $sanitized_array = array();
        foreach( $array_values as $value ) {
	        $sanitized_array[] = self::sanitize_int( $value );
        }

        return $sanitized_array;
    }

	public static function sanitize_html_tag( $tag, $default='div' ) {
		$tag = trim( $tag );
		$tag = substr( $tag, 0, 4);
		return in_array( $tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'] ) ? $tag : $default;
	}

	/**
	 * Decode and sanitize form fields.
	 *
	 * @param $form
	 * @param $all_fields_specs
	 * @return array
	 */
	public static function retrieve_and_sanitize_form( $form, $all_fields_specs ) {
		if ( empty( $form ) ) {
			return array();
		}

		// first urldecode()
		if ( is_string( $form ) ) {
			parse_str( $form, $submitted_fields );
		} else {
			$submitted_fields = $form;
		}

		// now sanitize each field
		$sanitized_fields = array();
		foreach( $submitted_fields as $submitted_key => $submitted_value ) {

			$field_type = empty($all_fields_specs[$submitted_key]['type']) ? '' : $all_fields_specs[$submitted_key]['type'];

			if ( $field_type == EPKB_Input_Filter::WP_EDITOR ) {
				$sanitized_fields[$submitted_key] = wp_kses( $submitted_value, self::get_extended_html_tags() );

			} elseif ( $field_type == EPKB_Input_Filter::TYPOGRAPHY ) {
				$sanitized_fields[$submitted_key] = EPKB_Input_Filter::sanitize_typography( $submitted_value );

			} elseif ( $field_type == EPKB_Input_Filter::TEXT && ! empty($all_fields_specs[$submitted_key]['allowed_tags']) ) {
				// text input with allowed tags 
				$sanitized_fields[$submitted_key] = wp_kses( $submitted_value, $all_fields_specs[$submitted_key]['allowed_tags'] );

			} else {
				$sanitized_fields[$submitted_key] = sanitize_text_field( $submitted_value );
			}

		}

		return $sanitized_fields;
	}

	/**
	 * Return ints and comma only.
	 *
	 * @param $text
	 * @param String $default
	 * @return String|$default
	 */
	public static function sanitize_comma_separated_ints( $text, $default='' ) {

		if ( empty( $text ) || ! is_string( $text ) ) {
			return $default;
		}

		$text = preg_replace( '/[^0-9 \,_]/', '', $text );

		return empty( $text ) ? $default : $text;
	}

	/**
	 * Retrieve value from POST or GET
	 *
	 * @param $key
	 * @param string $default
	 * @param string $value_type How to treat and sanitize value. Values: text, url
	 * @param int $max_length
	 * @return array|string - empty if not found
	 */
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Verified elsewhere.
	public static function post( $key, $default = '', $value_type = 'text', $max_length = 0 ) {

		if ( isset( $_POST[$key] ) ) {
			return self::post_sanitize( $key, $default, $value_type, $max_length );
		}

		if ( isset( $_GET[$key] ) ) {
			return self::get_sanitize( $key, $default, $value_type, $max_length );
		}

		return $default;
	}

	/**
	 * Retrieve value from POST or GET
	 *
	 * @param $key
	 * @param string $default
	 * @param string $value_type How to treat and sanitize value. Values: text, url
	 * @param int $max_length
	 * @return array|string - empty if not found
	 */
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Verified elsewhere.
	private static function post_sanitize( $key, $default = '', $value_type = 'text', $max_length = 0 ) {

		if ( $_POST[$key] === null || is_object( $_POST[$key] )  ) {
			return $default;
		}

		// config is sanitizing with its own specs separately
		if ( $value_type == 'db-config' ) {
			return $_POST[$key];
		}

		// config is sanitizing with its own specs separately
		if ( $value_type == 'db-config-json' ) {
			$decoded_value = $key == 'epkb_article_views_counter' ? json_decode( stripslashes( $_COOKIE[$key] ), true ) : json_decode( stripcslashes( $_POST[$key] ), true );
			return empty( $decoded_value ) ? $default : $decoded_value;
		}

		if ( is_array( $_POST[$key] ) ) {
			return array_map( 'sanitize_text_field', $_POST[$key] );
		}

		if ( $value_type == 'text-area' ) {
			$value = sanitize_textarea_field( stripslashes( $_POST[$key] ) );  // do not strip line breaks
		} else if ( $value_type == 'email' ) {
			$value = sanitize_email( $_POST[$key] );  // strips out all characters that are not allowable in an email
		} else if ( $value_type == 'url' ) {
			$value = sanitize_url( urldecode( $_POST[$key] ) );
		} else if ( $value_type == 'wp_editor' ) {
			$value = wp_kses( $_POST[$key], self::get_extended_html_tags() );
		} else {
			$value = sanitize_text_field( stripslashes( $_POST[$key] ) );
		}

		// optionally limit the value by length
		if ( ! empty( $max_length ) ) {
			$value = self::substr( $value, 0, $max_length );
		}

		return $value;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	public static function request_key( $key, $default = '' ) {

		if ( isset( $_REQUEST[$key] ) && is_string( $_REQUEST[$key] ) ) {
			return sanitize_key( $_REQUEST[$key] );
		}

		return $default;
	}

	/**
	 * Retrieve value from POST or GET
	 *
	 * @param $key
	 * @param string $default
	 * @param string $value_type How to treat and sanitize value. Values: text, url
	 * @param int $max_length
	 * @return array|string - empty if not found
	 */
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	public static function get( $key, $default = '', $value_type = 'text', $max_length = 0 ) {

		if ( isset( $_GET[$key] ) ) {
			return self::get_sanitize( $key, $default, $value_type, $max_length );
		}

		if ( isset( $_POST[$key] ) ) {
			return self::post_sanitize( $key, $default, $value_type, $max_length );
		}

		return $default;
	}

	/**
	 * Retrieve value from POST or GET
	 *
	 * @param $key
	 * @param string $default
	 * @param string $value_type How to treat and sanitize value. Values: text, url
	 * @param int $max_length
	 * @return array|string - empty if not found
	 */
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	private static function get_sanitize( $key, $default = '', $value_type = 'text', $max_length = 0 ) {

		if ( $_GET[$key] === null || is_object( $_GET[$key] )  ) {
			return $default;
		}

		// config is sanitizing with its own specs separately
		if ( $value_type == 'db-config' ) {
			return $_GET[$key];
		}

		// config is sanitizing with its own specs separately
		if ( $value_type == 'db-config-json' ) {
			$decoded_value = json_decode( stripcslashes( $_GET[$key] ), true );
			return empty( $decoded_value ) ? $default : $decoded_value;
		}

		if ( is_array( $_GET[$key] ) ) {
			return array_map( 'sanitize_text_field', $_GET[$key] );
		}

		if ( $value_type == 'text-area' ) {
			$value = sanitize_textarea_field( stripslashes( $_GET[$key] ) );  // do not strip line breaks
		} else if ( $value_type == 'email' ) {
			$value = sanitize_email( $_GET[$key] );  // strips out all characters that are not allowable in an email
		} else if ( $value_type == 'url' ) {
			$value = sanitize_url( urldecode( $_GET[$key] ) );
		} else if ( $value_type == 'wp_editor' ) {
			$value = wp_kses( $_GET[$key], self::get_extended_html_tags() );
		} else {
			$value = sanitize_text_field( stripslashes( $_GET[$key] ) );
		}

		// optionally limit value by length
		if ( ! empty( $max_length ) ) {
			$value = self::substr( $value, 0, $max_length );
		}

		return $value;
	}

	public static function sanitize_array( $value ) {
		$result = [];
		foreach ( $value as $key => $val ) {

			// can be 2-dimension array
			if ( is_array( $val ) ) {

				if ( empty( $result[ $key ] ) ) {
					$result[ $key ] = [];
				}

				foreach ( $val as $key_2 => $val_2 ) {
					$result[ $key ][ $key_2 ] = sanitize_text_field( stripslashes( $val_2 ) );
				}

			} else {
				$result[ $key ] = sanitize_text_field( stripslashes( $val ) );
			}
		}

		return $result;
	}


	/**************************************************************************************************************************
	 *
	 *                     GET/SAVE/UPDATE AN OPTION
	 *
	 *************************************************************************************************************************/

	/**
	 * Get KB-SPECIFIC option. Function adds KB ID suffix. Prefix represent core or ADD-ON prefix.
	 *
	 * @param $kb_id - assuming it is a valid ID
	 * @param $option_name - without kb suffix
	 * @param $default - use if KB option not found
	 * @param bool $is_array - ensure returned value is an array, otherwise return default
	 * @return string|array or default
	 */
	public static function get_kb_option( $kb_id, $option_name, $default, $is_array=false ) {
		$full_option_name = $option_name . '_' . $kb_id;
		return self::get_wp_option( $full_option_name, $default, $is_array );
	}

	/**
	 * Use to get:
	 *  a) PLUGIN-WIDE option not specific to any KB with e p k b prefix.
	 *  b) ADD-ON-SPECIFIC option with ADD-ON prefix.
	 *  b) KB-SPECIFIC configuration with e p k b prefix and KB ID suffix.
	 *
	 * @param $option_name
	 * @param $default
	 * @param bool|false $is_array
	 * @param bool $return_error
	 *
	 * @return array|string|WP_Error or default or error if $return_error is true
	 */
	public static function get_wp_option( $option_name, $default, $is_array=false, $return_error=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( isset(self::$wp_options_cache[$option_name]) ) {
			return self::$wp_options_cache[$option_name];
		}

		// retrieve KB setting from WP Options table
		$option = get_option( $option_name );
		if ( empty( $option ) || ( $is_array && ! is_array( $option ) ) ) {
			$option = false;
		}

		// return found KB setting
		if ( ! empty( $option ) ) {
			return $option;
		}

		// retrieve specific WP option
		$option = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", $option_name ) );
		if ( $option !== null ) {
			$option = maybe_unserialize( $option );
		}

		if ( $return_error && $option === null && ! empty( $wpdb->last_error ) ) {
			$wpdb_last_error = $wpdb->last_error;   // add_log changes last_error so store it first
			EPKB_Logging::add_log( "DB failure: " . $wpdb_last_error, 'Option Name: ' . $option_name );
			return new WP_Error( __( 'Error occurred', 'echo-knowledge-base' ), $wpdb_last_error );
		}

		// if WP option is missing then return defaults
		if ( $option === null || ( $is_array && ! is_array( $option ) ) ) {
			return $default;
		}

		self::$wp_options_cache[$option_name] = $option;

		return $option;
	}

	/**
	 * Save KB-SPECIFIC option. Function adds KB ID suffix. Prefix represent core or ADD-ON prefix.
	 *
	 * @param $kb_id - assuming it is a valid ID
	 * @param $option_name - without kb suffix
	 * @param $option_value
	 *
	 * @return mixed|WP_Error if option cannot be serialized or db insert failed
	 */
	public static function save_kb_option( $kb_id, $option_name, $option_value ) {
		$full_option_name = $option_name . '_' . $kb_id;
		return self::save_wp_option( $full_option_name, $option_value );
	}

	/**
	 * Save KB-SPECIFIC option. Function adds KB ID suffix. Prefix represent core or ADD-ON prefix.
	 * This version uses default WP saving for option
	 *
	 * @param $kb_id - assuming it is a valid ID
	 * @param $option_name - without kb suffix
	 * @param array $option_value
	 *
	 * @return bool True if the value was updated, false otherwise
	 */
	public static function save_kb_option2( $kb_id, $option_name, $option_value ) {
		$full_option_name = $option_name . '_' . $kb_id;
		return update_option( $full_option_name, $option_value );
	}

	/**
	 * Save WP option
	 * @param $option_name
	 * @param $option_value
	 * @return mixed|WP_Error
	 */
	public static function save_wp_option( $option_name, $option_value ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// do not store null
		if ( $option_value === null ) {
			$option_value = '';
		}

		// return if no change in configuration detected
		$old_value = get_option( $option_name );
		if ( $option_value === $old_value || maybe_serialize( $option_value ) === maybe_serialize( $old_value ) ) {
			return $option_value;
		}

		// update configuration if possible
		$result = update_option( $option_name, $option_value );
		if ( $result !== false ) {
			return $option_value;
		}

		// add or update the option
		$serialized_value = $option_value;

		// check if array or object type of option can be properly serialized
		if ( is_array( $option_value ) || is_object( $option_value ) ) {
			$serialized_value = maybe_serialize( $option_value );
			if ( empty( $serialized_value ) ) {
				return new WP_Error( '434', esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' ' . $option_name );
			}
		}

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->options (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s)
 												 ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)",
												$option_name, $serialized_value, 'no' ) );
		if ( $result === false ) {
			EPKB_Logging::add_log( 'Failed to update option', $option_name );
			return new WP_Error( '435', 'Failed to update option ' . $option_name );
		}

		self::$wp_options_cache[$option_name] = $option_value;

		return $option_value;
	}


    /**************************************************************************************************************************
     *
     *                     DATABASE
     *
     *************************************************************************************************************************/

	/**
	 * Get given Post Metadata
	 *
	 * @param $post_id
	 * @param $meta_key
	 * @param $default
	 * @param bool|false $is_array
	 * @param bool|WP_Error $return_error
	 *
	 * @return array|string|WP_Error or default or error if $return_error is true
	 */
	public static function get_postmeta( $post_id, $meta_key, $default, $is_array=false, $return_error=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( isset( self::$postmeta[$post_id][$meta_key] ) ) {
			return self::$postmeta[$post_id][$meta_key];
		}

		if ( ! self::is_positive_int( $post_id) ) {
			return $return_error ? new WP_Error( esc_html__( 'Error occurred', 'echo-knowledge-base' ), self::get_variable_string( $post_id ) ) : $default;
		}

		// retrieve specific option
		$option = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d and meta_key = %s", $post_id, $meta_key ) );
		if ($option !== null ) {
			$option = maybe_unserialize( $option );
		}

		if ( $return_error && $option === null && ! empty($wpdb->last_error) ) {
			$wpdb_last_error = $wpdb->last_error;   // add_log changes last_error so store it first
			EPKB_Logging::add_log( "DB failure: " . $wpdb_last_error, 'Meta Key: ' . $meta_key );
			return new WP_Error( 'Error occurred', $wpdb_last_error );
		}

		// if the option is missing then return defaults
		if ( $option === null || ( $is_array && ! is_array($option) ) ) {
			return $default;
		}

		self::$postmeta[$post_id][$meta_key] = $option;

		return $option;
	}

	/**
	 * Save or Insert Post Metadata
	 *
	 * @param $post_id
	 * @param $meta_key
	 * @param $meta_value
	 * @param $sanitized
	 *
	 * @return mixed|WP_Error
	 */
	public static function save_postmeta( $post_id, $meta_key, $meta_value, $sanitized ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( ! self::is_positive_int( $post_id) ) {
			return new WP_Error( esc_html__( 'Error occurred', 'echo-knowledge-base' ), self::get_variable_string( $post_id ) );
		}

		if ( $sanitized !== true ) {
			return new WP_Error( '433', esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' ' . $meta_key );
		}

		// do not store null
		if ( $meta_value === null ) {
			$meta_value = '';
		}

		// add or update the option
		$serialized_value = $meta_value;
		if ( is_array( $meta_value ) || is_object( $meta_value ) ) {
			$serialized_value = maybe_serialize($meta_value);
			if ( empty($serialized_value) ) {
				return new WP_Error( '434', esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' ' . $meta_key );
			}
		}

		// check if the meta field already exists before doing 'upsert'
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) );
		if ( $result === null && ! empty($wpdb->last_error) ) {
			$wpdb_last_error = $wpdb->last_error;   // add_log changes last_error so store it first
			EPKB_Logging::add_log( "DB failure: " . $wpdb_last_error );
			return new WP_Error( esc_html__( 'Error occurred', 'echo-knowledge-base' ), $wpdb_last_error);
		}

		// INSERT or UPDATE the meta field
		if ( empty($result) ) {
			if ( false === $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta (`meta_key`, `meta_value`, `post_id`) VALUES (%s, %s, %d)", $meta_key, $serialized_value, $post_id ) ) ) {
				EPKB_Logging::add_log("Failed to insert meta data. ", $meta_key);
				return new WP_Error( '33', esc_html__( 'Error occurred', 'echo-knowledge-base' ) );
			}
		} else {
			if ( false === $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = %s WHERE meta_key = %s AND post_id = %d", $serialized_value, $meta_key, $post_id ) ) ) {
				EPKB_Logging::add_log("Failed to update meta data. ", $meta_key);
				return new WP_Error( '33', esc_html__( 'Error occurred', 'echo-knowledge-base' ) );
			}
		}

		if ( $result === false ) {
			EPKB_Logging::add_log( 'Failed to update meta key', $meta_key );
			return new WP_Error( '435', esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' ' . $meta_key );
		}

		self::$postmeta[$post_id][$meta_key] = $meta_value;

		return $meta_value;
	}

	/**
	 * Delete given Post Metadata
	 *
	 * @param $post_id
	 * @param $meta_key
	 *
	 * @return bool
	 */
	public static function delete_postmeta( $post_id, $meta_key ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( ! self::is_positive_int( $post_id) ) {
			return false;
		}

		// delete specific option
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d and meta_key = %s", $post_id, $meta_key ) ) ) {
			EPKB_Logging::add_log( "Could not delete post '" . self::get_variable_string($meta_key) . "'' metadata: ", $post_id);
			return false;
		}

		return true;
	}


	/**************************************************************************************************************************
	 *
	 *                     OTHER
	 *
	 *************************************************************************************************************************/

	/**
	 * Return string representation of given variable for logging purposes
	 *
	 * @param $var
	 *
	 * @return string
	 */
	public static function get_variable_string( $var ) {

		if ( ! is_array($var) ) {
			return self::get_variable_not_array( $var );
		}

		if ( empty($var) ) {
			return '[empty]';
		}

		$output = 'array';
		$ix = 0;
		foreach ($var as $key => $value) {

            if ( $ix++ > 10 ) {
                $output .= '[.....]';
                break;
            }

			$output .= "[" . $key . " => ";
			if ( ! is_array($value) ) {
				$output .= self::get_variable_not_array( $value ) . "]";
				continue;
			}

			$ix2 = 0;
			$output .= "[";
			$first = true;
			foreach($value as $key2 => $value2) {
                if ( $ix2++ > 10 ) {
                    $output .= '[.....]';
                    break;
                }

				if ( is_array($value2) ) {
                    $output .= print_r($value2, true);
                } else {
					$output .= ( $first ? '' : ', ' ) . $key2 . " => " . self::get_variable_not_array( $value2 );
					$first = false;
					continue;
				}
            }
			$output .= "]]";
		}

		return $output;
	}

	private static function get_variable_not_array( $var ) {

		if ( $var === null ) {
			return '<' . 'null' . '>';
		}

		if ( ! isset($var) ) {
            /** @noinspection HtmlUnknownAttribute */
            return '<' . 'not set' . '>';
		}

		if ( is_array($var) ) {
			return empty($var) ? '[]' : '[...]';
		}

		if ( is_object( $var ) ) {
			return '<' . get_class($var) . '>';
		}

		if ( is_bool( $var ) ) {
			return $var ? 'TRUE' : 'FALSE';
		}

		if ( is_string( $var ) ) {
			return empty( $var ) ? '<empty string>' : $var;
		}

		if ( is_numeric( $var ) ) {
			return $var;
		}

		return '<' . 'unknown' . '>';
	}

	/**
	 * Array1 VALUES NOT IN array2
	 *
	 * @param array $array1
	 * @param array $array2
	 *
	 * @return array of values in array1 NOT in array2
	 */
	public static function diff_two_dimentional_arrays( array $array1, array $array2 ) {

		if ( empty($array1) ) {
			return array();
		}

		if ( empty($array2) ) {
			return $array1;
		}

		// flatten first array
		foreach( $array1 as $key => $value ) {
			if ( is_array($value) ) {
				$tmp_value = '';
				foreach( $value as $tmp ) {
					$tmp_value .= ( empty($tmp_value) ? '' : ',' ) . ( empty($tmp) ? '' : $tmp );
				}
				$array1[$key] = $tmp_value;
			}
		}

		// flatten second array
		foreach( $array2 as $key => $value ) {
			if ( is_array($value) ) {
				$tmp_value = '';
				foreach( $value as $tmp ) {
					$tmp_value .= ( empty($tmp_value) ? '' : ',' ) . ( empty($tmp) ? '' : $tmp );
				}
				$array2[$key] = $tmp_value;
			}
		}

		return array_diff_assoc($array1, $array2);
	}

	public static function mb_strtolower( $string ) {
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
	}

	public static function is_logged_on() {
		$user = self::get_current_user();
		return ! empty( $user );
	}

	/**
	 * Determine if current user is WP administrator WITHOUT calling current_user_can()
	 *
	 * @param null $user
	 * @return bool
	 */
	public static function is_user_admin( $user=null ) {

		// get current user
		$user = empty( $user ) ? self::get_current_user() : $user;
		if ( empty( $user ) || empty( $user->roles ) || empty( $user->allcaps ) ) {
			return false;
		}

		return in_array( 'administrator', $user->roles ) || array_key_exists( 'manage_options', $user->allcaps );
	}

	/**
	 * Get current user.
	 *
	 * @return null|WP_User
	 */
	public static function get_current_user() {

		$user = null;
		if ( function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
		}

		// is user not logged in? user ID is 0 if not logged
		if ( empty( $user ) || ! $user instanceof WP_User || empty( $user->ID ) ) {
			$user = null;
		}

		return $user;
	}

	/**
	 * Check first installed version. Return true if $version less or equal than first installed version.
	 * @param $kb_config
	 * @param $version
	 * @return bool
	 */
	public static function is_new_user( $kb_config, $version ) {
		$plugin_first_version = empty( $kb_config['first_plugin_version'] ) ? '11.30.0' : $kb_config['first_plugin_version'];
		return ! version_compare( $plugin_first_version, $version, '<' );
	}

	/**
	 * Output inline CSS style based on configuration.
	 *
	 * @param string $styles A list of Configuration Setting styles
	 * @param $config
	 * @return string
	 */
	public static function get_inline_style( $styles, $config ) {

		if ( empty( $styles ) || ! is_string( $styles ) ) {
			return '';
		}

		$style_array = explode(',', $styles);
		if ( empty( $style_array ) ) {
			return '';
		}

		$output = 'style="';
		foreach( $style_array as $style ) {

			$key_value = array_map( 'trim', explode(':', $style) );
			if ( empty( $key_value[0] ) ) {
				continue;
			}

			if ( $key_value[0] != 'typography' ) {
				$output .= esc_attr( $key_value[0] ) . ': ';
			}

			// true if using config value
			if ( count( $key_value ) == 2 && isset( $key_value[1] ) ) {

				if ( $key_value[0] == 'justify-content' ) {

					if ( $key_value[1] == 'left' ) {
						$output .= 'flex-start';
					} else if ( $key_value[1] == 'right' ) {
						$output .= 'flex-end';
					} else {
						$output .= esc_attr( $key_value[1] );
					}

				} else if ( $key_value[0] == ' text-align' ) {

					if ( $key_value[1] == 'left' ) {
						$output .= 'start';
					} else if ( $key_value[1] == 'right' ) {
						$output .= 'end';
					} else {
						$output .= esc_attr( $key_value[1] );
					}

				} else {
					$output .= esc_attr( $key_value[1] );
				}

			} else if ( $key_value[0] == 'typography' && isset( $config[$key_value[2]] ) ) { // typography field

				$typography_values = array_merge( EPKB_Typography::$typography_defaults, $config[$key_value[2]] );
				if ( ! empty( $typography_values['font-family'] ) ) {
					$output .= 'font-family:' . esc_attr( $typography_values['font-family'] ) . ';';
				}

				if ( ! empty( $typography_values['font-size'] ) ) {
					$output .= 'font-size:' . esc_attr( $typography_values['font-size'] . $typography_values['font-size-units'] ) . ';';
				}

				if ( ! empty( $typography_values['font-weight'] ) ) {
					$output .= 'font-weight:' . esc_attr( $typography_values['font-weight'] ) . ';';
				}

			} else if ( isset( $key_value[2] ) && isset( $config[$key_value[2]] ) ) {

				if ( $key_value[0] == 'justify-content' ) {

					if ( $config[ $key_value[2] ] == 'left' ) {
						$output .= 'flex-start';
					} else if ( $config[ $key_value[2] ] == 'right' ) {
						$output .= 'flex-end';
					} else {
						$output .= esc_attr( $config[ $key_value[2] ] );
					}

				} else if ( $key_value[0] == 'text-align' ) {

					if ( $config[ $key_value[2] ] == 'left' ) {
						$output .= 'start';
					} else if ( $config[ $key_value[2] ] == 'right' ) {
						$output .= 'end';
					} else {
						$output .= esc_attr( $config[ $key_value[2] ] );
					}

				} else {
					$output .= esc_attr( $config[ $key_value[2] ] );
				}

				switch ( $key_value[0] ) {
					case 'border-radius':
					case 'border-width':
					case 'border-bottom-width':
					case 'border-top-left-radius':
					case 'border-top-right-radius':
					case 'border-bottom-left-radius':
					case 'border-bottom-right-radius':
					case 'min-height':
					case 'max-height':
					case 'height':
					case 'padding-left':
					case 'padding-right':
					case 'padding-top':
					case 'padding-bottom':
					case 'margin':
					case 'margin-top':
					case 'margin-right':
					case 'margin-bottom':
					case 'margin-left':
					case 'font-size':
						$output .= 'px';
						break;
				}
			}

			if ( $key_value[0] != 'typography' ) {
				$output .= '; ';
			}
		}

		return ' ' . trim( $output ) . '" ';
	}

	/**
	 * Output CSS classes based on configuration.
	 *
	 * @param $classes
	 * @param $config
	 * @return string
	 */
	public static function get_css_class( $classes, $config ) {

		if ( empty( $classes ) || ! is_string( $classes ) ) {
			return '';
		}

		$output = ' class="';
		foreach( array_map( 'trim', explode(',', $classes) ) as $class ) {
			$class_name = trim( str_replace( ':', '', $class ) );
			$is_config = $class != $class_name;

			if ( $is_config && empty( $config[$class_name] ) ) {
				continue;
			}

			$output .= ( $is_config ? esc_attr( $config[$class_name] ) : esc_attr( $class ) ) . ' ';
		}
		return trim( $output ) . '"';
	}

	public static function get_font_css( $kb_config, $config_name, $font_param_name, $delta=0 ) {

		if ( empty( $kb_config[$config_name][$font_param_name] ) ) {
			return $font_param_name . ': inherit;';
		}

		$value = empty( $delta ) ? $kb_config[$config_name][$font_param_name] : intval( $kb_config[$config_name][$font_param_name] ) + $delta;

		return $font_param_name . ': ' . $value . ( $font_param_name == 'font-size' ? 'px' : '' ) . ' !important;';
	}

	public static function get_typography_config( $typography ) {
		$typography_styles = '';

		if ( empty( $typography ) || ! is_array( $typography ) ) {
			$typography = EPKB_Typography::$typography_defaults;
		}

		if ( ! empty( $typography['font-family'] ) ) {
			$typography_styles .= 'font-family: ' . $typography['font-family'] . ' !important;';
		}

		if ( ! empty( $typography['font-size'] ) && empty( $typography['font-size-units'] ) ) {
			$typography_styles .= 'font-size: ' . $typography['font-size'] . 'px !important;';
		}

		if ( ! empty( $typography['font-size'] ) && ! empty( $typography['font-size-units'] ) ) {
			$typography_styles .= 'font-size: ' . $typography['font-size'] . $typography['font-size-units'] . ' !important;';
		}

		if ( ! empty( $typography['font-weight'] ) ) {
			$typography_styles .= 'font-weight: ' . $typography['font-weight'] . ' !important;';
		}

		return $typography_styles;
	}

	public static function get_single_article_link( $kb_config, $title, $article_id, $type, $seq_no='' ) {
		static $epkb_single_article_link = null;

		$title_style_escaped = '';

		switch( $type ) {

			case 'Article_Sidebar':
				$a_tag_class = 'epkb-sidebar-article';
				$outer_span = 'eckb-article-title';
				$icon_class = 'eckb-article-title__icon ep_font_icon_document';
				$article_color_escaped = self::get_inline_style( 'color:: sidebar_article_font_color', $kb_config );
				$icon_color_escaped = self::get_inline_style( 'color:: sidebar_article_icon_color', $kb_config );
				$title_style_escaped = self::get_inline_style( 'typography:: sidebar_section_body_typography', $kb_config );
				$title_class = 'eckb-article-title__text';
				break;

			case 'Category_Archive_Page':
				$a_tag_class = 'epkb-ml-article-container';
				$outer_span = 'epkb-article-inner';
				$icon_class = 'epkb-article__icon ep_font_icon_document';
				$article_color_escaped = self::get_inline_style( 'color:: sidebar_article_font_color', $kb_config );
				$icon_color_escaped = self::get_inline_style( 'color:: sidebar_article_icon_color', $kb_config );
				$title_class = 'epkb-article__text';
				break;

			case EPKB_Layout::CLASSIC_LAYOUT:
			case EPKB_Layout::DRILL_DOWN_LAYOUT;
			case 'Module':
				$a_tag_class = 'epkb-ml-article-container';
				$outer_span = 'epkb-article-inner';
				$icon_class = 'epkb-article__icon ep_font_icon_document';
				$setting_names = EPKB_Core_Utilities::get_style_setting_name( $kb_config['kb_main_page_layout'] );
				$article_color_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_font_color'] , $kb_config );
				$icon_color_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_icon_color'] , $kb_config );
				$title_class = 'epkb-article__text';
				break;

			case EPKB_Layout::BASIC_LAYOUT:
			case EPKB_Layout::TABS_LAYOUT:
			case EPKB_Layout::CATEGORIES_LAYOUT:
			default:
				$a_tag_class = 'epkb-mp-article';
				$outer_span = 'eckb-article-title';
				$icon_class = 'eckb-article-title__icon ep_font_icon_document';
				$setting_names = EPKB_Core_Utilities::get_style_setting_name( $kb_config['kb_main_page_layout'] );
				$article_color_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_font_color'] , $kb_config );
				$icon_color_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_icon_color'] , $kb_config );
				$title_class = 'eckb-article-title__text';
		}

		// handle any add-on content
		$title_attr_escaped = '';
		$new_tab = '';
		$link = '';
		if ( has_filter( 'eckb_single_article_filter' ) ) {

			$result = apply_filters( 'eckb_single_article_filter', $article_id, array( $kb_config['id'], $title, $outer_span, $article_color_escaped, $icon_color_escaped ) );

			// keep for old compatibility for links to output separately
			if ( ! empty( $result ) && $result === true ) {
				return;
			}

			if ( is_array( $result ) && isset( $result['url_value'] ) ) {
				$link = $result['url_value'];
				$title_attr_escaped = 'title="' . esc_attr( $result['title_attr_value'] ) . '"';
				$new_tab = $result['new_tab'];
				$icon_class = 'eckb-article-title__icon epkbfa epkbfa-' . $result['icon'];
			}
		}

		// custom article list icon
		if ( empty( $link ) && EPKB_Utilities::is_elegant_layouts_enabled() && has_filter( 'eckb_article_list_icon_filter' ) ) {

			if ( empty( $epkb_single_article_link ) ) {

				$result = apply_filters( 'eckb_article_list_icon_filter', $article_id, array( $kb_config['id'], $type) );
				if ( ! empty( $result['icon'] ) ) {
					$icon_class = $result['icon'];
					$epkb_single_article_link = $icon_class;
				}
			} else {
				$icon_class = $epkb_single_article_link;
			}
		}

		if ( empty( $link ) ) {
			$link = get_permalink( $article_id );
			if ( ! has_filter('article_with_seq_no_in_url_enable') ) {
				$link = empty( $seq_no ) || $seq_no < 2 ? $link : add_query_arg('seq_no', $seq_no, $link);
				$link = empty( $link ) || is_wp_error( $link ) ? '' : $link;
			}
		}

		$icon_toggle_class = '';
		$icon_toggle = $type == 'Article_Sidebar' || $type == 'Category_Archive_Page' ? 'sidebar_article_icon_toggle' : 'article_icon_toggle';
		if ( $kb_config[$icon_toggle] == 'off' ) {
			$icon_class = '';
			$icon_toggle_class = 'epkb-article--no-icon';
		}

		// output link if wizard is not on otherwise output span
		if ( ! empty( $_GET['ordering-wizard-on'] ) ) { ?>
			<span class="<?php echo esc_attr( $a_tag_class ) . ' ' . esc_attr( $icon_toggle_class ); ?>" data-kb-article-id="<?php echo esc_attr( $article_id ); ?>">
				<span class="<?php echo esc_attr( $icon_class ); ?>"></span>
				<span class="<?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></span>
			</span> <?php
			return;
		}   ?>

		<a href="<?php echo esc_url( $link ); ?>" <?php echo $title_attr_escaped; ?> class="<?php echo esc_attr( $a_tag_class ) . ' ' . esc_attr( $icon_toggle_class ); ?>"
		            data-kb-article-id="<?php echo esc_attr( $article_id ); ?>" <?php echo ( empty( $new_tab ) ? '' : 'target="_blank" rel="noopener noreferrer"' ); ?>>
			<span class="<?php echo esc_attr( $outer_span ); ?>" <?php echo $article_color_escaped; ?> >
				<span class="<?php echo esc_attr( $icon_class ); ?>" <?php echo $icon_color_escaped; ?> aria-hidden="true"></span>
				<span class="<?php echo esc_attr( $title_class ); ?>" <?php echo $title_style_escaped; ?>><?php echo esc_html( $title ); ?></span>
			</span> <?php
			if ( $type == 'Category_Archive_Page' && ! empty( $kb_config['archive_content_articles_arrow_toggle'] ) && $kb_config['archive_content_articles_arrow_toggle'] == 'on' ) { ?>
				<span class="eckb-category-archive-arrow epkbfa epkbfa-arrow-right"></span> <?php
			}   ?>
		</a>    <?php
	}

	/**
	 * Check if Access Manager is considered active.
	 *
	 * @param bool $is_active_check_only
	 * @return bool
	 */
	public static function is_amag_on( $is_active_check_only=true ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( defined( 'AMAG_PLUGIN_NAME' ) ) {
			return true;
		}

		if ( $is_active_check_only ) {
			return false;
		}

		$table = $wpdb->prefix . 'am'.'gr_kb_groups';
		$result = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );

		return ( ! empty( $result ) && ( $table == $result ) );
	}
	
	/**
	 * Check if given articles belong to the currently selected langauge. Return ones that are.
	 * @param $articles
	 * @param bool $are_posts
	 * @return array
	 */
	public static function is_wpml_article_active( $articles, $are_posts=false ) {

		$article_ids = $articles;
		if ( $are_posts ) {
			$article_ids = array();
			foreach( $articles as $article ) {
				$article_ids[] = empty($article->ID) ? 0 : $article->ID;
			}
		}

		$current_lang = apply_filters( 'wpml_current_language', NULL );
		$current_article_ids = array();
		foreach( $article_ids as $article_id ) {
			$args = array( 'element_id' => $article_id, 'element_type' => 'post' );
			$article_lang = apply_filters( 'wpml_element_language_code', null, $args );
			if ( $article_lang == $current_lang ) {
				$current_article_ids[] = $article_id;
			}
		}

		return $current_article_ids;
	}

	/**
	 * Is WPML enabled?
	 *
	 * @param array $config
	 * @return bool
	 */
	public static function is_wpml_enabled( $config=array() ) {
		return ! empty( $config['wpml_is_enabled'] ) && $config['wpml_is_enabled'] === 'on' && ! defined( 'AMAG_PLUGIN_NAME' );
	}

	/**
	 * Is WPML plugin activated?
	 *
	 * @return bool
	 */
	public static function is_wpml_plugin_active() {
		return defined( 'ICL_SITEPRESS_VERSION' );
	}

	public static function is_advanced_search_enabled( $kb_config=array() ) {

		if ( ! defined('AS'.'EA_PLUGIN_NAME') ) {
			return false;
		}

		return empty( $kb_config ) || (
		       $kb_config['kb_articles_common_path'] != 'demo-1-knowledge-base-basic-layout' &&
		       $kb_config['kb_articles_common_path'] != 'demo-2-knowledge-base-basic-layout' &&
		       $kb_config['kb_articles_common_path'] != 'demo-3-knowledge-base-tabs-layout' &&
		       $kb_config['kb_articles_common_path'] != 'demo-4-knowledge-base-tabs-layout' &&
		       $kb_config['kb_articles_common_path'] != 'demo-12-knowledge-base-image-layout' );
	}

	public static function is_article_rating_enabled() {
	   return defined( 'EP' . 'RF_PLUGIN_NAME' );
	}

	public static function is_elegant_layouts_enabled() {
		return defined('E'.'LAY_PLUGIN_NAME');
	}

	public static function is_multiple_kbs_enabled() {
		return defined('E'.'MKB_PLUGIN_NAME');
	}

	public static function is_article_features_enabled() {
		return defined('E'.'ART_PLUGIN_NAME');
	}

	public static function is_export_import_enabled() {
		return defined('E'.'PIE_PLUGIN_NAME');
	}

	public static function is_creative_addons_widgets_enabled() {
		return defined( 'CREATIVE_ADDONS_VERSION' ) && defined( 'ELEMENTOR_VERSION' );
	}

	public static function is_link_editor_enabled() {
		return defined('KB'.'LK_PLUGIN_NAME');
	}

	public static function is_kb_widgets_enabled() {
		return defined('WI'.'DG_PLUGIN_NAME');
	}

	public static function is_help_dialog_enabled() {
		return defined('EP'.'HD_PLUGIN_NAME');
	}

	public static function is_help_dialog_pro_enabled() {
		return defined('EP'.'HP_PLUGIN_NAME');
	}

	public static function is_knowledge_base_enabled() {
		return defined('EP'.'KB_PLUGIN_NAME');
	}

	public static function is_groups_enabled() {
		return defined('AM'.'GP_PLUGIN_NAME');
	}

	public static function is_custom_roles_enabled() {
		return defined('AM'.'CR_PLUGIN_NAME');
	}

	public static function is_link_editor( $post ) {
		return ! empty($post->post_mime_type) && ( $post->post_mime_type == 'kb_link' or $post->post_mime_type == 'kblink' );
	}

	/**
	 * Check if certain KB's plugin is enabled
	 *
	 * @param $plugin_id
	 * @return bool
	 */
	public static function is_plugin_enabled( $plugin_id ) {

		switch ( $plugin_id ) {
			case 'am'.'gr' :
			case 'core'    : return true;
			case 'pro'     : return self::is_help_dialog_pro_enabled();
			case 'em'.'kb' : return self::is_multiple_kbs_enabled();
			case 'ep'.'ie' : return self::is_export_import_enabled();
			case 'el'.'ay' : return self::is_elegant_layouts_enabled();
			case 'kb'.'lk' : return self::is_link_editor_enabled();
			case 'ep'.'rf' : return self::is_article_rating_enabled();
			case 'as'.'ea' : return self::is_advanced_search_enabled();
			case 'wi'.'dg' : return self::is_kb_widgets_enabled();
			case 'ep'.'hd' : return self::is_help_dialog_enabled();
			case 'cr'.'el' : return self::is_creative_addons_widgets_enabled();
			case 'am'.'gp' : return self::is_groups_enabled();
			case 'am'.'cr' : return self::is_custom_roles_enabled();
			default: return false;
		}
	}

	/**
	 * Check if Classic Editor plugin is active.
	 * By KAGG Design
	 * @return bool
	 */
	public static function is_block_editor_active() {
		// Gutenberg plugin is installed and activated.
		$gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

		// Block editor since 5.0.
		$block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		if ( self::is_classic_editor_plugin_active() ) {
			$editor_option       = get_option( 'classic-editor-replace' );
			$block_editor_active = array( 'no-replace', 'block' );
			return in_array( $editor_option, $block_editor_active, true );
		}

		return true;
	}

	/**
	 * Check if Classic Editor plugin is active.
	 * By KAGG Design
	 * @return bool
	 */
	public static function is_classic_editor_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'classic-editor/classic-editor.php' );
	}

	public static function is_kb_main_page() {
		global $eckb_is_kb_main_page;
		return isset( $eckb_is_kb_main_page ) && $eckb_is_kb_main_page;
	}

	/**
	 * Send email using WordPress email facility.
	 *
	 * @param $message
	 * @param string $to_support_email - usually admin or support
	 * @param string $reply_to_email - usually customer email
	 * @param string $reply_to_name - usually customer name
	 * @param string $subject - which functionality is this email coming from
	 *
	 * @return string - return '' if email sent otherwise return error message
	 */
	public static function send_email( $message, $is_message_html, $to_support_email='', $reply_to_email='', $reply_to_name='', $subject='' ) {

		// validate MESSAGE
		if ( empty( $message ) || strlen( $message ) > 5000 ) {
			EPKB_Logging::add_log( 'Invalid or too long email message', $message );
			return esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (0)';
		}

		$message = wp_kses_post( $message );

		// validate TO email
		if ( empty( $to_support_email ) ) { // send to admin if empty
			$to_support_email = get_option( 'admin_email' );
		}

		$to_support_email = sanitize_email( $to_support_email );
		if ( empty( $to_support_email ) || strlen( $to_support_email) > 100 ) {
			return esc_html__( 'Invalid email', 'echo-knowledge-base' ) . ' (1)';
		}

		if ( ! is_email( $to_support_email ) ) {
			return esc_html__( 'Invalid email', 'echo-knowledge-base' ) . ' (2)';
		}

		// validate REPLY TO email/name
		if ( empty( $reply_to_email ) ) {
			$reply_to_email = get_option( 'admin_email' );
		}

		$reply_to_email = sanitize_email( $reply_to_email );
		if ( empty( $reply_to_email ) || strlen( $reply_to_email ) > 100 ) {
			return esc_html__( 'Invalid email', 'echo-knowledge-base' ) . ' (3)';
		}
		
		if ( ! is_email( $reply_to_email ) ) {
			return esc_html__( 'Invalid email', 'echo-knowledge-base' ) . ' (4)';
		}
		
		if ( strlen( $reply_to_name ) > 100 ) {
			return esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (5)';
		}
		
		$reply_to_name = sanitize_text_field( $reply_to_name );

		// validate SUBJECT
		$subject = sanitize_text_field( $subject );
		if ( empty( $subject ) ) {
			$subject = esc_html__( 'New message', 'echo-knowledge-base' ) . ' ' . esc_html_x( 'from', 'email sent from someone', 'echo-knowledge-base' ) . ' ' . esc_attr( get_bloginfo( 'name' ) );
		}

		if ( strlen( $subject ) > 200 ) {
			return esc_html__( 'Invalid subject', 'echo-knowledge-base' );
		}

		if ( strlen( $message ) > 10000 ) {
			return esc_html__( 'Email message is too long', 'echo-knowledge-base' );
		}

		// setup Email header
		$from_name = get_bloginfo( 'name' ); // Site title (set in Settings > General)
		$from_email = get_option( 'admin_email' );
		$headers = array(
			"From: {$from_name} <{$from_email}>\r\n",
			"Reply-To: {$reply_to_name} <{$reply_to_email}>\r\n",
			"Content-Type: text/html; charset=utf-8\r\n",
		);

		// setup Email message if not HTML
		if ( ! $is_message_html ) {
			$message = '
				<html>
					<body>' .
						esc_html( $message ) . '
					</body>
				</html>';

			// convert text to HTML - clickable links, turning line breaks into <p> and <br/> tags
			//$message = wpautop( make_clickable( $message ), false );
			$message = str_replace( '&#038;', '&amp;', $message );
			$message = str_replace( ["\r\n", '\r\n', "\n", '\n', "\r", '\r'], '<br />', $message );
		}

		global /** @var WP_Error $epkb_email_error - email error from WordPress wp_mail() function */
		$epkb_email_error;

		$epkb_email_error = false;
		add_action( 'wp_mail_failed', [ 'EPKB_Utilities', 'check_email_errors' ] );

		// we to add filter to allow HTML in the email content to make sure the content type was not changed by third-party code
		add_filter( 'wp_mail_content_type', array( 'EPKB_Utilities', 'set_html_content_type' ), 999 );

		// send email
		$result = wp_mail( $to_support_email, $subject, $message, $headers );

		// remove filter that allows HTML in the email content
		remove_filter( 'wp_mail_content_type', array( 'EPKB_Utilities', 'set_html_content_type' ), 999 );
		remove_action( 'wp_mail_failed', [ 'EPKB_Utilities', 'check_email_errors' ] );

		// Log email errors if need
		$error_message = esc_html__( 'Failed to send the email.', 'echo-knowledge-base' );

		/** $epkb_email_error @ WP_Error */
		if ( is_wp_error( $epkb_email_error ) ) {
			EPKB_Logging::add_log( 'Email error: ' . $epkb_email_error->get_error_message() );
			$error_message = $epkb_email_error->get_error_message();
		}

		return $result ? '' : $error_message;
	}

	/**
	 * Called by WordPress to store Error when submitting email to the Mail Server
	 * @param WP_Error$error
	 */
	public static function check_email_errors( $error ) {
		global $epkb_email_error;
		$epkb_email_error = $error;
	}

	public static function set_html_content_type( $content_type ) {
		return 'text/html';
	}

	/**
	 * Get Category Link
	 * @param $category_id
	 * @param string $taxonomy
	 * @return string
	 */
	public static function get_term_url( $category_id, $taxonomy = '' ) {

		$category_url = empty( $taxonomy ) ? get_term_link( $category_id ) : get_term_link( $category_id, $taxonomy );
		if ( empty($category_url) || is_wp_error( $category_url )) {
			return '';
		}

		return $category_url;
	}

	/**
     * Return current page url without domain. Working only for the admin side
	 * @return string
	 */
    public static function get_current_admin_url() {

        $uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );
        if ( empty( $uri ) ) {
            return '';
        }

        return remove_query_arg( array( '_wpnonce', '_wc_notice_nonce', 'wc_db_update', 'wc_db_update_nonce', 'wc-hide-notice' ), $uri );
	}

	/**
	 * Return allowed HTML tags and attributes for front-end and wp editor
	 *
	 * @param $is_frontend
	 * @return array
	 */
	public static function get_extended_html_tags( $is_frontend=false ) {

		$extended_post_tags = [];
		if ( $is_frontend || self::is_user_allowed_unfiltered_html() ) {
			$extended_post_tags = [
				'source' => [
					'src' => true,
					'type' => true
				],
				'iframe' => self::get_admin_ui_extended_html_attributes()
			];
		}

		return array_merge( wp_kses_allowed_html( 'post' ), $extended_post_tags );
    }

	/**
	 * Return allowed HTML tags and attributes for ADMIN UI
	 *
	 * @param $extra_tags
	 * @return array
	 */
	public static function get_admin_ui_extended_html_tags( $extra_tags=[] ) {

		$extended_post_tags = [
			'input'     => self::get_admin_ui_extended_html_attributes(),
			'select'    => self::get_admin_ui_extended_html_attributes(),
			'option'    => self::get_admin_ui_extended_html_attributes(),
			'form'      => self::get_admin_ui_extended_html_attributes()
		];

		foreach( $extra_tags as $extra_tag ) {
			$extended_post_tags += [ $extra_tag => self::get_admin_ui_extended_html_attributes() ];
		}

		global $allowedposttags;
		$allowed_post_tags = empty( $allowedposttags ) ? wp_kses_allowed_html( 'post' ) : $allowedposttags;

		return array_merge( $allowed_post_tags, $extended_post_tags );
	}

	/**
	 * Return list of HTML attributes allowed in admin UI
	 *
	 * @return bool[]
	 */
	private static function get_admin_ui_extended_html_attributes() {
		return [
			'name'              => true,
			'type'              => true,
			'value'             => true,
			'class'             => true,
			'style'             => true,
			'data-*'            => true,
			'id'                => true,
			'checked'           => true,
			'selected'          => true,
			'method'            => true,
			'src'               => true,
			'width'             => true,
			'height'            => true,
			'title'             => true,
			'frameborder'       => true,
			'allow'             => true,
			'allowfullscreen'   => true,
			'enctype' 			=> true,
			'autocomplete'      => true,
			'action'            => true,
			'required'          => true,
			'placeholder'       => true
		];
	}

	/**
	 * Add specific CSS styles allowed in admin UI
	 *
	 * @param $safe_style_css
	 * @return array
	 */
	public static function admin_ui_safe_style_css( $safe_style_css ) {
		return array_merge( $safe_style_css, array(
			'display',
		) );
	}

	/**
	 * Wrapper for WordPress 'wp_kses' to use for HTML filtering in admin UI
	 *
	 * @param $html
	 * @param array $extra_tags
	 * @return string
	 */
	public static function admin_ui_wp_kses( $html, $extra_tags=[] ) {

		// allow specific CSS styles that are disabled by default in WordPress core
		add_filter( 'safe_style_css', array( 'EPKB_Utilities', 'admin_ui_safe_style_css' ) );

		$sanitized_html = wp_kses( $html, self::get_admin_ui_extended_html_tags( $extra_tags ) );

		// disallow specific CSS styles
		remove_filter( 'safe_style_css', array( 'EPKB_Utilities', 'admin_ui_safe_style_css' ) );

		return $sanitized_html;
	}

	public static function is_user_allowed_unfiltered_html() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return false;
		}

		return current_user_can( 'unfiltered_html' ) || current_user_can( 'manage_options' );
	}

	/**
	 * Adjust list of safe CSS properties that allowed in wp_kses
	 *
	 * @param $styles
	 *
	 * @return mixed
	 */
	public static function safe_inline_css_properties( $styles ) {
		$styles[] = 'display';
		return $styles;
	}

	/**
	 * Get classes based on theme name for specific targeting
	 * @param $prefix
	 * @return string
	 */
	public static function get_active_theme_classes( $prefix = 'mp' ) {

		$current_theme = wp_get_theme();
		if ( ! empty( $current_theme ) && is_object( $current_theme ) && get_class( $current_theme ) != 'WP_Theme' ) {
			return '';
		}

		// get parent theme class if this is child theme
		$current_theme_parent = $current_theme->parent();
		if ( ! empty( $current_theme_parent ) && is_object( $current_theme_parent ) && get_class( $current_theme_parent ) == 'WP_Theme' ) {
			return 'eckb_' . $prefix . '_active_theme_' . $current_theme_parent->get_stylesheet();
		}

		return 'eckb_' . $prefix . '_active_theme_' . $current_theme->get_stylesheet();
	}

	/**
	 * Private articles - return 'true' if the current user can view this private article
	 *
	 * @param $article_id
	 *
	 * @return bool
	 */
	public static function is_article_allowed_for_current_user( $article_id ) {

		// AMAG handles this separately
		if ( self::is_amag_on() ) {
			return true;
		}

		$article = get_post( $article_id );

		// disallow article that failed to retrieve
		if ( empty( $article ) || empty( $article->post_status ) ) {
			return false;
		}

		// only check permissions for private articles
		if ( $article->post_status != 'private' ) {
			return true;
		}

		// for private articles user needs to be logged in
		if ( ! is_user_logged_in() ) {
			return false;
		}

		return current_user_can( 'read_private_posts' ) || get_current_user_id() == $article->post_author;
	}

	/**
	 * Same code as wp_slash has in WP-5.5.0 (introduced in WP-3.6.0)
	 * The wp_slash_strings_only is now deprecated (introduced in WP-5.3.0),
	 * But the wp_slash does not handle non-string values until WP-5.5.0
	 * We need this function to support newest and oldest WP versions
	 *
	 * @param string|array $value
	 *
	 * @return string|array
	 */
	public static function slash_strings_only( $value ) {

		if ( is_array( $value ) ) {
			$value = array_map( 'wp_slash', $value );
		}

		if ( is_string( $value ) ) {
			return addslashes( $value );
		}

		return $value;
	}

	public static function get_post_type_labels( $disallowed_post_types, $allowed_post_types=[], $exclude_kb=false ) {

		$cpts = [];

		$wp_cpts = get_post_types( [ 'public' => true ], 'object' );
		foreach ( $wp_cpts as $post_type => $post_type_object ) {

			if ( $exclude_kb && EPKB_KB_Handler::is_kb_post_type( $post_type ) ) {
				continue;
			}

			if ( in_array( $post_type, $disallowed_post_types ) ) {
				continue;
			}

			if ( ! EPKB_KB_Handler::is_kb_post_type( $post_type ) && ! empty( $allowed_post_types ) && ! in_array( $post_type, $allowed_post_types ) ) {
				continue;
			}

			$cpts[ $post_type ] = self::get_post_type_label( $post_type_object );
		}

		return $cpts;
	}

	/**
	 * Return pretty label for post type
	 *
	 * @param $post_type_object - see get_post_types() results
	 * @return string
	 */
	public static function get_post_type_label( $post_type_object ) {

		// Standard
		if ( $post_type_object->name == 'post' ) {
			return esc_html__( 'Post' );
		}

		if ( $post_type_object->name == 'page' ) {
			return esc_html__( 'Page' );
		}

		if ( in_array( $post_type_object->name, ['ip_lesson', 'ip_quiz', 'ip_question', 'ip_course'] ) ) {
			return $post_type_object->label . ' (LearnPress)';
		}

		if ( in_array( $post_type_object->name, ['sfwd-lessons', 'sfwd-quiz', 'sfwd-topic'] ) ) {
			return $post_type_object->label . ' (LearnDash)';
		}

		if ( in_array( $post_type_object->name, ['forum', 'topic'] ) ) {
			return $post_type_object->label . ' (bbPress)';
		}

		// BasePress
		if ( $post_type_object->name == 'knowledgebase' && count($post_type_object->taxonomies) == 1 && isset( $post_type_object->taxonomies[0] ) && $post_type_object->taxonomies[0] == 'knowledgebase_cat' ) {
			return $post_type_object->label . ' (BasePress)';
		}

		// weDocs
		if ( $post_type_object->name == 'docs' && count($post_type_object->taxonomies) == 1 && isset( $post_type_object->taxonomies[0] ) && $post_type_object->taxonomies[0] == 'doc_tag' ) {
			return $post_type_object->label . ' (WeDocs)';
		}

		// BetterDocs
		if ( $post_type_object->name == 'docs' && count($post_type_object->taxonomies) == 0 ) {
			return $post_type_object->label . ' (BetterDocs)';
		}

		// Woocommerce
		if ( $post_type_object->name == 'product' ) {
			return $post_type_object->label . ' (WooCommerce)';
		}

		return $post_type_object->label;
	}

	/**
	 * Check if the current theme is a block theme.
	 * @return bool
	 */
	public static function is_block_theme() {
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return (bool) wp_is_block_theme();
		}
		if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
			return (bool) gutenberg_is_fse_theme();
		}

		return false;
	}

	/**
	 * Removes comments, spaces, and line breaks form CSS. Limits single line length. Removes empty rule blocks.
	 *
	 * @param $css
	 * @return string
	 */
	public static function minify_css( $css ) {

		if ( ! is_string( $css ) ) {
			return '';
		}

		// Remove comments
		$css = preg_replace( '/(?<!\\\\)\/\*(.*?)\*(?<!\\\\)\//Ss', '', $css );

		// Process quoted unquotable attribute selectors to unquote them. Covers most common cases.
		// Likelyhood of a quoted attribute selector being a substring in a string: Very very low.
		$css = preg_replace( '/\[\s*([a-z][a-z-]+)\s*([\*\|\^\$~]?=)\s*[\'"](-?[a-z_][a-z0-9-_]+)[\'"]\s*\]/Ssi', '[$1$2$3]', $css );

		// Normalize all whitespace strings to single spaces
		$css = preg_replace('/\s+/S', ' ', $css );

		// Remove spaces before the things that should not have spaces before them.
		$css = preg_replace( '/ ([@{};>+)\]~=,\/\n])/S', '$1', $css );

		// Remove the spaces after the things that should not have spaces after them.
		$css = preg_replace( '/([{}:;>+(\[~=,\/\n]) /S', '$1', $css );

		// Find a fraction that may used in some @media queries such as: (min-aspect-ratio: 1/1)
		// Add token to add the "/" back in later
		$css = preg_replace( '/\(([a-z-]+):([0-9]+)\/([0-9]+)\)/Si', '($1:$2'. '_CSSMIN_QF_' .'$3)', $css );

		// Remove empty rule blocks up to 2 levels deep.
		$css = preg_replace( array_fill( 0, 2, '/(\{)[^{};\/\n]+\{\}/S' ), '$1', $css );
		$css = preg_replace( '/[^{};\/\n]+\{\}/S', '', $css );

		// Restore fraction
		$css = str_replace( '_CSSMIN_QF_', '/', $css );

		// Some source control tools don't like it when files containing lines longer
		// than, say 8000 characters, are checked in. The linebreak option is used in
		// that case to split long lines after a specific column.
		$line_break_position = 5000;
		$l = strlen( $css );
		$offset = $line_break_position;
		while ( preg_match( '/(?<!\\\\)\}(?!\n)/S', $css, $matches, PREG_OFFSET_CAPTURE, $offset ) ) {
			$matchIndex = (int)$matches[0][1];
			$css = substr_replace( $css, "\n", $matchIndex + 1, 0 );
			$offset = $matchIndex + 2 + $line_break_position;
			$l += 1;
			if ( $offset > $l ) {
				break;
			}
		}

		return $css;
	}

	/**
	 * Encrypt data using OpenSSL functions if available
	 * @param $data
	 * @return string
	 */
	public static function encrypt_data( $data = '' ) {

		if ( empty( $data ) ) {
			return $data;
		}

		// Check if OpenSSL functions are available
		if ( ! function_exists( 'openssl_encrypt' ) || ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
			return base64_encode( $data );
		}

		// Generate a key
		$key = wp_hash( 'epkb_encrypt' );
		if ( empty( $key ) ) {
			return base64_encode( $data );
		}

		// Get the cipher method and IV length
		$cipher_method = 'aes-256-cbc';
		$iv_length = openssl_cipher_iv_length( $cipher_method );
		if ( empty( $iv_length ) ) {
			return base64_encode( $data );
		}

		// Generate an initialization vector
		$iv = openssl_random_pseudo_bytes( $iv_length );

		// Encrypt the data
		$encrypted_data = openssl_encrypt( $data, $cipher_method, $key, 0, $iv );
		if ( empty( $encrypted_data ) ) {
			return base64_encode( $data );
		}

		// Concatenate the encrypted data and IV
		$encrypted_string = base64_encode( $encrypted_data . '::' . $iv );

		return $encrypted_string;
	}

	/**
	 * Decrypt data using OpenSSL functions if available
	 * @param $data
	 * @return false|string
	 */
	public static function decrypt_data( $data = '' ) {

		if ( empty( $data ) ) {
			return $data;
		}

		// Validate input data
		if ( ! is_string( $data ) ) {
			return false;
		}

		// Base64 decode the data
		$decoded_data = base64_decode( $data, true );
		if ( empty( $decoded_data ) ) {
			return false;
		}

		// Split the encrypted data and IV; if missing we have just base64 encoded data
		$separator = '::';
		$parts = explode( $separator, $decoded_data, 2 );
		if ( count( $parts ) !== 2 ) {
			return $decoded_data;
		}

		// Check if OpenSSL functions are available
		if ( ! function_exists( 'openssl_decrypt' ) || ! function_exists( 'openssl_cipher_iv_length' ) ) {
			return false;
		}

		// Generate the decryption key
		$key = wp_hash( 'epkb_encrypt' );
		if ( empty( $key ) ) {
			return false;
		}

		list( $encrypted_data, $iv ) = $parts;

		// Decrypt the data
		$decrypted_data = openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );
		if ( empty( $decrypted_data ) ) {
			return false;
		}

		return $decrypted_data;
	}

	/**
	 * Defines whether the given URL is internal or external
	 * @param $url
	 * @return bool
	 */
	public static function is_internal_url( $url ) {

		// get the site's host
		$site_host = parse_url( home_url(), PHP_URL_HOST );

		// parse the URL to get its host
		$url_host = parse_url( $url, PHP_URL_HOST );

		// handle relative URLs (no host) - relative URLs are considered internal
		if ( empty( $url_host ) ) {
			return true;
		}

		// Compare hosts (case-insensitive)
		return strcasecmp( $site_host, $url_host ) === 0;
	}
}