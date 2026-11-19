<?php

su_add_shortcode(
	array(
		'id'       => 'conditional',
		'callback' => 'su_shortcode_conditional',
		'icon'     => 'code-fork',
		'name'     => __( 'Conditional content', 'shortcodes-ultimate' ),
		'type'     => 'wrap',
		'group'    => 'other',
		'atts'     => array(
			'condition'         => array(
				'type'    => 'select',
				'values'  => array(
					''                     => __( 'Select condition', 'shortcodes-ultimate' ),
					'logged_in'            => __( 'User is logged in', 'shortcodes-ultimate' ),
					'logged_out'           => __( 'User is logged out', 'shortcodes-ultimate' ),
					'user_role'            => __( 'User role', 'shortcodes-ultimate' ),
					'device'               => __( 'Device', 'shortcodes-ultimate' ),
					'language'             => __( 'Language', 'shortcodes-ultimate' ),
					'date'                 => __( 'Date', 'shortcodes-ultimate' ),
					'time'                 => __( 'Time', 'shortcodes-ultimate' ),
					'datetime'             => __( 'Date and time', 'shortcodes-ultimate' ),
					'cookie'               => __( 'Cookie', 'shortcodes-ultimate' ),
					'url'                  => __( 'URL', 'shortcodes-ultimate' ),
					'referrer'             => __( 'HTTP referrer', 'shortcodes-ultimate' ),
					'post_type'            => __( 'Post type', 'shortcodes-ultimate' ),
					'post_category'        => __( 'Post category', 'shortcodes-ultimate' ),
					'woocommerce_purchase' => __( 'WooCommerce purchase', 'shortcodes-ultimate' ),
				),
				'default' => '',
				'name'    => __( 'Condition', 'shortcodes-ultimate' ),
				'desc'    => __( 'Select a condition, then fill in its related fields. Fields for other conditions are ignored.', 'shortcodes-ultimate' ),
			),
			'operator'          => array(
				'type'    => 'select',
				'values'  => array(
					'is'     => __( 'Is', 'shortcodes-ultimate' ),
					'is_not' => __( 'Is not', 'shortcodes-ultimate' ),
				),
				'default' => 'is',
				'name'    => __( 'Operator', 'shortcodes-ultimate' ),
				'desc'    => __( 'Use “Is not” to invert the selected condition.', 'shortcodes-ultimate' ),
			),
			'role'              => array(
				'depends' => array( 'condition' => 'user_role' ),
				'default' => '',
				'name'    => __( 'User role', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the User role condition. Enter one or more role slugs separated by commas, for example administrator,editor. User-dependent content may be affected by full-page caching.', 'shortcodes-ultimate' ),
			),
			'device'            => array(
				'depends' => array( 'condition' => 'device' ),
				'type'    => 'select',
				'values'  => array(
					'mobile'  => __( 'Mobile', 'shortcodes-ultimate' ),
					'desktop' => __( 'Desktop', 'shortcodes-ultimate' ),
				),
				'default' => 'mobile',
				'name'    => __( 'Device', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Device condition. Detection is performed on the server, not by viewport width, and may be affected by full-page caching or a CDN.', 'shortcodes-ultimate' ),
			),
			'language'          => array(
				'depends' => array( 'condition' => 'language' ),
				'default' => '',
				'name'    => __( 'Language', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Language condition. Enter a locale such as en_US or a two-letter language code such as en.', 'shortcodes-ultimate' ),
			),
			'cookie_name'       => array(
				'depends' => array( 'condition' => 'cookie' ),
				'default' => '',
				'name'    => __( 'Cookie name', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Cookie condition. Show content when this cookie exists. Cookie-dependent content may be affected by full-page caching.', 'shortcodes-ultimate' ),
			),
			'cookie_value'      => array(
				'depends' => array( 'condition' => 'cookie' ),
				'default' => '',
				'name'    => __( 'Cookie value', 'shortcodes-ultimate' ),
				'desc'    => __( 'Optional. When set, the cookie value must match this value exactly.', 'shortcodes-ultimate' ),
			),
			'product_id'        => array(
				'depends' => array( 'condition' => 'woocommerce_purchase' ),
				'default' => '',
				'name'    => __( 'Product ID', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the WooCommerce purchase condition. Enter one or more product IDs separated by commas. The condition is available to logged-in customers and may be affected by full-page caching.', 'shortcodes-ultimate' ),
			),
			'date_from'         => array(
				'depends' => array( 'condition' => 'date' ),
				'type'    => 'date_picker',
				'default' => '',
				'name'    => __( 'Date from', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Date condition. Start date in YYYY-MM-DD format, using the WordPress site timezone.', 'shortcodes-ultimate' ),
			),
			'date_to'           => array(
				'depends' => array( 'condition' => 'date' ),
				'type'    => 'date_picker',
				'default' => '',
				'name'    => __( 'Date to', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Date condition. End date in YYYY-MM-DD format, using the WordPress site timezone.', 'shortcodes-ultimate' ),
			),
			'time_from'         => array(
				'depends' => array( 'condition' => 'time' ),
				'type'    => 'time_picker',
				'default' => '',
				'name'    => __( 'Time from', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Time condition. Start time in HH:MM format. A range such as 22:00–06:00 crosses midnight.', 'shortcodes-ultimate' ),
			),
			'time_to'           => array(
				'depends' => array( 'condition' => 'time' ),
				'type'    => 'time_picker',
				'default' => '',
				'name'    => __( 'Time to', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Time condition. End time in HH:MM format, using the WordPress site timezone.', 'shortcodes-ultimate' ),
			),
			'datetime_from'     => array(
				'depends' => array( 'condition' => 'datetime' ),
				'default' => '',
				'name'    => __( 'Date and time from', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Date and time condition. Start in YYYY-MM-DD HH:MM format, using the WordPress site timezone.', 'shortcodes-ultimate' ),
			),
			'datetime_to'       => array(
				'depends' => array( 'condition' => 'datetime' ),
				'default' => '',
				'name'    => __( 'Date and time to', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Date and time condition. End in YYYY-MM-DD HH:MM format, using the WordPress site timezone.', 'shortcodes-ultimate' ),
			),
			'url_contains'      => array(
				'depends' => array( 'condition' => 'url' ),
				'default' => '',
				'name'    => __( 'URL contains', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the URL condition. Enter text that must occur in the current request URL.', 'shortcodes-ultimate' ),
			),
			'referrer_contains' => array(
				'depends' => array( 'condition' => 'referrer' ),
				'default' => '',
				'name'    => __( 'Referrer contains', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the HTTP referrer condition. Referrers are optional browser data and can be missing or inaccurate.', 'shortcodes-ultimate' ),
			),
			'post_type'         => array(
				'depends' => array( 'condition' => 'post_type' ),
				'default' => '',
				'name'    => __( 'Post type', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Post type condition. Enter one or more post type slugs separated by commas.', 'shortcodes-ultimate' ),
			),
			'category'          => array(
				'depends' => array( 'condition' => 'post_category' ),
				'default' => '',
				'name'    => __( 'Category', 'shortcodes-ultimate' ),
				'desc'    => __( 'For the Post category condition. Enter one or more category slugs or IDs separated by commas.', 'shortcodes-ultimate' ),
			),
		),
		'content'  => __( 'Conditionally displayed content', 'shortcodes-ultimate' ),
		'desc'     => __( 'Show content only when specified conditions are met.', 'shortcodes-ultimate' ),
		'note'     => __( 'Only fields related to the selected condition are used. Conditions based on users, devices, cookies, or purchases may be incompatible with full-page caching. This shortcode does not disable caching automatically.', 'shortcodes-ultimate' ),
	)
);

/**
 * Sanitize Conditional shortcode attributes.
 *
 * @param array $atts Shortcode attributes.
 * @return array Sanitized attributes.
 */
function su_shortcode_conditional_sanitize_atts( $atts ) {

	foreach ( $atts as $name => $value ) {
		$atts[ $name ] = is_scalar( $value )
			? trim( sanitize_text_field( (string) $value ) )
			: '';
	}

	$atts['condition'] = sanitize_key( $atts['condition'] );
	$atts['operator']  = 'is_not' === sanitize_key( $atts['operator'] ) ? 'is_not' : 'is';

	return $atts;

}

/**
 * Parse a comma-separated list into sanitized keys.
 *
 * @param string $value Comma-separated list.
 * @return array Sanitized keys.
 */
function su_shortcode_conditional_parse_keys( $value ) {

	$keys = array_map( 'sanitize_key', explode( ',', $value ) );
	$keys = array_filter( $keys );

	return array_values( array_unique( $keys ) );

}

/**
 * Check whether a condition name is registered.
 *
 * @param string $condition Condition name.
 * @param array  $atts      Sanitized shortcode attributes.
 * @return bool Whether the condition is registered.
 */
function su_shortcode_conditional_is_supported_condition( $condition, $atts ) {

	$conditions = array(
		'logged_in',
		'logged_out',
		'user_role',
		'device',
		'language',
		'date',
		'time',
		'datetime',
		'cookie',
		'url',
		'referrer',
		'post_type',
		'post_category',
		'woocommerce_purchase',
	);

	/**
	 * Filter registered Conditional shortcode condition names.
	 *
	 * Add a sanitized condition name here and calculate its value with the
	 * shortcodes_ultimate_conditional_result filter.
	 *
	 * @param array $conditions Registered condition names.
	 * @param array $atts       Sanitized shortcode attributes.
	 */
	$conditions = (array) apply_filters(
		'shortcodes_ultimate_conditional_conditions',
		$conditions,
		$atts
	);
	$conditions = array_filter( $conditions, 'is_scalar' );
	$conditions = array_map( 'sanitize_key', $conditions );

	return in_array( $condition, $conditions, true );

}

/**
 * Parse and strictly validate a date or date and time value.
 *
 * @param string $value  Date value.
 * @param string $format Expected format.
 * @return DateTimeImmutable|false Parsed value or false on failure.
 */
function su_shortcode_conditional_parse_datetime( $value, $format ) {

	$datetime = DateTimeImmutable::createFromFormat(
		'!' . $format,
		$value,
		wp_timezone()
	);
	$errors = DateTimeImmutable::getLastErrors();

	if (
		false === $datetime ||
		( is_array( $errors ) && ( $errors['warning_count'] || $errors['error_count'] ) ) ||
		$datetime->format( $format ) !== $value
	) {
		return false;
	}

	return $datetime;

}

/**
 * Check an inclusive date or date and time range in the site timezone.
 *
 * @param string $from   Lower boundary.
 * @param string $to     Upper boundary.
 * @param string $format Boundary format.
 * @return bool Whether the current date/time is in the range.
 */
function su_shortcode_conditional_check_datetime_range( $from, $to, $format ) {

	if ( '' === $from && '' === $to ) {
		return false;
	}

	$current = current_datetime()->format( $format );

	if ( '' !== $from ) {

		$from_datetime = su_shortcode_conditional_parse_datetime( $from, $format );

		if ( false === $from_datetime || $current < $from_datetime->format( $format ) ) {
			return false;
		}

	}

	if ( '' !== $to ) {

		$to_datetime = su_shortcode_conditional_parse_datetime( $to, $format );

		if ( false === $to_datetime || $current > $to_datetime->format( $format ) ) {
			return false;
		}

	}

	return true;

}

/**
 * Convert a strictly formatted HH:MM value to minutes after midnight.
 *
 * @param string $time Time value.
 * @return int|false Number of minutes or false for invalid input.
 */
function su_shortcode_conditional_time_to_minutes( $time ) {

	if ( ! preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $time ) ) {
		return false;
	}

	$parts = array_map( 'intval', explode( ':', $time ) );

	return ( $parts[0] * 60 ) + $parts[1];

}

/**
 * Check an inclusive time range in the site timezone.
 *
 * @param string $from Lower boundary.
 * @param string $to   Upper boundary.
 * @return bool Whether the current time is in the range.
 */
function su_shortcode_conditional_check_time_range( $from, $to ) {

	if ( '' === $from && '' === $to ) {
		return false;
	}

	$from_minutes = '' === $from
		? null
		: su_shortcode_conditional_time_to_minutes( $from );
	$to_minutes   = '' === $to
		? null
		: su_shortcode_conditional_time_to_minutes( $to );

	if ( false === $from_minutes || false === $to_minutes ) {
		return false;
	}

	$current = current_datetime();
	$now     = ( (int) $current->format( 'H' ) * 60 ) + (int) $current->format( 'i' );

	if ( null === $from_minutes ) {
		return $now <= $to_minutes;
	}

	if ( null === $to_minutes ) {
		return $now >= $from_minutes;
	}

	if ( $from_minutes <= $to_minutes ) {
		return $now >= $from_minutes && $now <= $to_minutes;
	}

	return $now >= $from_minutes || $now <= $to_minutes;

}

/**
 * Check whether the current customer bought any of the supplied products.
 *
 * @param string $product_ids Comma-separated product IDs.
 * @return bool Whether the customer bought a supplied product.
 */
function su_shortcode_conditional_check_woocommerce_purchase( $product_ids ) {

	if ( ! is_user_logged_in() || ! function_exists( 'wc_customer_bought_product' ) ) {
		return false;
	}

	$product_ids = array_map( 'absint', explode( ',', $product_ids ) );
	$product_ids = array_values( array_unique( array_filter( $product_ids ) ) );

	if ( empty( $product_ids ) ) {
		return false;
	}

	$user = wp_get_current_user();

	if ( ! $user->exists() ) {
		return false;
	}

	static $purchase_cache = array();

	foreach ( $product_ids as $product_id ) {

		$cache_key = $user->ID . ':' . $product_id;

		if ( ! array_key_exists( $cache_key, $purchase_cache ) ) {
			$purchase_cache[ $cache_key ] = (bool) wc_customer_bought_product(
				$user->user_email,
				$user->ID,
				$product_id
			);
		}

		if ( $purchase_cache[ $cache_key ] ) {
			return true;
		}

	}

	return false;

}

/**
 * Evaluate a Conditional shortcode condition.
 *
 * @param array $atts Sanitized shortcode attributes.
 * @return bool Condition result before applying the operator.
 */
function su_shortcode_conditional_check_condition( $atts ) {

	$condition = $atts['condition'];
	$result    = false;

	switch ( $condition ) {
		case 'logged_in':
			$result = is_user_logged_in();
			break;

		case 'logged_out':
			$result = ! is_user_logged_in();
			break;

		case 'user_role':
			$roles = su_shortcode_conditional_parse_keys( $atts['role'] );

			if ( is_user_logged_in() && ! empty( $roles ) ) {
				$result = (bool) array_intersect( wp_get_current_user()->roles, $roles );
			}
			break;

		case 'device':
			// wp_is_mobile() uses request headers, not the actual browser viewport width.
			if ( 'mobile' === $atts['device'] ) {
				$result = wp_is_mobile();
			} elseif ( 'desktop' === $atts['device'] ) {
				$result = ! wp_is_mobile();
			}
			break;

		case 'language':
			$language = strtolower( str_replace( '-', '_', $atts['language'] ) );
			$locale   = strtolower( str_replace( '-', '_', determine_locale() ) );

			if ( '' !== $language ) {
				$result = $language === $locale ||
					( 2 === strlen( $language ) && 0 === strpos( $locale, $language . '_' ) );
			}
			break;

		case 'date':
			$result = su_shortcode_conditional_check_datetime_range(
				$atts['date_from'],
				$atts['date_to'],
				'Y-m-d'
			);
			break;

		case 'time':
			$result = su_shortcode_conditional_check_time_range(
				$atts['time_from'],
				$atts['time_to']
			);
			break;

		case 'datetime':
			$result = su_shortcode_conditional_check_datetime_range(
				$atts['datetime_from'],
				$atts['datetime_to'],
				'Y-m-d H:i'
			);
			break;

		case 'cookie':
			$cookie_name = $atts['cookie_name'];

			if (
				'' !== $cookie_name &&
				preg_match( '/^[A-Za-z0-9!#$%&\'*+\-.^_`|~]+$/', $cookie_name ) &&
				isset( $_COOKIE[ $cookie_name ] )
			) {
				$cookie_value = wp_unslash( $_COOKIE[ $cookie_name ] );

				if ( is_scalar( $cookie_value ) ) {
					$cookie_value = sanitize_text_field( (string) $cookie_value );
					$result       = '' === $atts['cookie_value'] || $cookie_value === $atts['cookie_value'];
				}
			}
			break;

		case 'url':
			if (
				'' !== $atts['url_contains'] &&
				isset( $_SERVER['REQUEST_URI'] ) &&
				is_string( $_SERVER['REQUEST_URI'] )
			) {
				$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
				$current_url = esc_url_raw( home_url( $request_uri ) );
				$result      = '' !== $current_url && false !== strpos( $current_url, $atts['url_contains'] );
			}
			break;

		case 'referrer':
			if (
				'' !== $atts['referrer_contains'] &&
				isset( $_SERVER['HTTP_REFERER'] ) &&
				is_string( $_SERVER['HTTP_REFERER'] )
			) {
				$referrer = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
				$result   = '' !== $referrer && false !== strpos( $referrer, $atts['referrer_contains'] );
			}
			break;

		case 'post_type':
			$post_types = su_shortcode_conditional_parse_keys( $atts['post_type'] );
			$post_type  = get_post_type();
			$result     = false !== $post_type && in_array( $post_type, $post_types, true );
			break;

		case 'post_category':
			$post_id   = get_the_ID();
			$post_type = get_post_type( $post_id );
			$terms     = array();

			foreach ( explode( ',', $atts['category'] ) as $term ) {
				$term = trim( $term );

				if ( preg_match( '/^[0-9]+$/', $term ) && absint( $term ) ) {
					$terms[] = absint( $term );
				} elseif ( '' !== sanitize_title( $term ) ) {
					$terms[] = sanitize_title( $term );
				}
			}

			if (
				$post_id &&
				$post_type &&
				! empty( $terms ) &&
				taxonomy_exists( 'category' ) &&
				is_object_in_taxonomy( $post_type, 'category' )
			) {
				$result = has_term( $terms, 'category', $post_id );
			}
			break;

		case 'woocommerce_purchase':
			$result = su_shortcode_conditional_check_woocommerce_purchase( $atts['product_id'] );
			break;
	}

	/**
	 * Filter the condition result before the Is/Is not operator is applied.
	 *
	 * This filter can also implement custom condition names. Unknown conditions
	 * have a false result unless a callback changes it here.
	 *
	 * @param bool   $result    Condition result.
	 * @param string $condition Sanitized condition name.
	 * @param array  $atts      Sanitized shortcode attributes.
	 */
	return (bool) apply_filters(
		'shortcodes_ultimate_conditional_result',
		$result,
		$condition,
		$atts
	);

}

/**
 * Render the Conditional shortcode.
 *
 * @param array|null  $atts    Shortcode attributes.
 * @param string|null $content Enclosed content.
 * @return string Rendered content or an empty string.
 */
function su_shortcode_conditional( $atts = null, $content = null ) {

	$atts = su_parse_shortcode_atts( 'conditional', $atts );
	$atts = su_shortcode_conditional_sanitize_atts( $atts );

	if ( ! su_shortcode_conditional_is_supported_condition( $atts['condition'], $atts ) ) {
		return '';
	}

	$result = su_shortcode_conditional_check_condition( $atts );

	if ( 'is_not' === $atts['operator'] ) {
		$result = ! $result;
	}

	return $result && null !== $content
		? su_do_nested_shortcodes( $content, 'conditional' )
		: '';

}
