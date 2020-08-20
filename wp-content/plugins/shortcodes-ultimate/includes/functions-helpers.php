<?php

/**
 * Helper Functions.
 *
 * @since        5.0.5
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */

/**
 * Retrieve the URL of the plugin directory (with trailing slash).
 *
 * @since  5.0.5
 * @return string The URL of the plugin directory (with trailing slash).
 */
function su_get_plugin_url() {
	return plugin_dir_url( dirname( __FILE__ ) );
}

/**
 * Retrieve the filesystem path of the plugin directory (with trailing slash).
 *
 * @since  5.0.5
 * @return string The filesystem path of the plugin directory (with trailing slash).
 */
function su_get_plugin_path() {
	return plugin_dir_path( dirname( __FILE__ ) );
}

/**
 * Retrieve the current version of the plugin.
 *
 * @since  5.2.0
 * @return string The current verion of the plugin.
 */
function su_get_plugin_version() {
	return get_option( 'su_option_version', '0' );
}

/**
 * Get plugin config.
 *
 * @since  5.0.5
 * @param string  $key
 * @return mixed      Config data if found, False otherwise.
 */
function su_get_config( $key = null, $default = false ) {

	static $config = array();

	if (
		empty( $key ) ||
		preg_match( '/^(?!-)[a-z0-9-_]+(?<!-)(\/(?!-)[a-z0-9-_]+(?<!-))*$/', $key ) !== 1
	) {
		return $default;
	}

	if ( isset( $config[ $key ] ) ) {
		return $config[ $key ];
	}

	$config_file = su_get_plugin_path() . 'includes/config/' . $key . '.php';

	if ( ! file_exists( $config_file ) ) {
		return $default;
	}

	$config[ $key ] = include $config_file;

	return $config[ $key ];

}

/**
 * Create an error message.
 *
 * @since  5.0.5
 * @param string  $title   Error title.
 * @param string  $message Error message.
 * @return string          Error message markup.
 */
function su_error_message( $title = '', $message = '' ) {

	if ( ! su_current_user_can_insert() ) {
		return;
	}

	if ( $title ) {
		$title = "<strong>${title}:</strong> ";
	}

	return sprintf(
		'<p class="su-error" style="padding:5px 10px;color:#ff685e;border-left:3px solid #ff685e;background:#fff">%1$s%2$s</p>',
		$title,
		$message
	);

}

/**
 * Conditional check if current user can use the plugin.
 *
 * @since 5.4.0
 * @return bool True if user is allowed to use the plugin, False otherwise.
 */
function su_current_user_can_insert() {

	$required_capability = (string) get_option(
		'su_option_generator_access',
		'manage_options'
	);

	return current_user_can( $required_capability );

}

/**
 * Validate filter callback name.
 *
 * @since  5.0.5
 * @param string  $filter Filter callback name.
 * @return boolean         True if filter name contains word 'filter', False otherwise.
 */
function su_is_filter_safe( $filter ) {
	return is_string( $filter ) && false !== strpos( $filter, 'filter' );
}

/**
 * Range converter.
 *
 * Converts string ranges like '1, 3-5' into arrays like [1, 3, 4, 5].
 *
 * @since  5.0.5
 * @param string  $string Range string.
 * @return array          Parsed range.
 */
function su_parse_range( $string = '' ) {

	$parsed = array();

	foreach ( explode( ',', $string ) as $range ) {

		if ( strpos( $range, '-' ) === false ) {
			$parsed[] = intval( $range );
			continue;
		}

		$range = explode( '-', $range );

		if ( ! is_numeric( $range[0] ) ) {
			$range[0] = 0;
		}

		if ( ! is_numeric( $range[1] ) ) {
			$range[1] = 0;
		}

		foreach ( range( $range[0], $range[1] ) as $value ) {
			$parsed[] = $value;
		}

	}

	sort( $parsed );
	$parsed = array_unique( $parsed );

	return $parsed;

}

/**
 * Extract CSS class name(s) from shortcode $atts and prepend with a space.
 *
 * @since  5.0.5
 * @param array   $atts Shortcode atts.
 * @return string       Extra CSS class(es) prepended by a space.
 */
if ( ! function_exists( 'su_get_css_class' ) ) {

	function su_get_css_class( $atts ) {
		return $atts['class'] ? ' ' . trim( $atts['class'] ) : '';
	}

}

/**
 * Helper function to force enqueuing of the shortcode generator assets and
 * templates.
 *
 * Usage example:
 * `add_action( 'admin_init', 'su_enqueue_generator' );`
 *
 * @since 5.1.0
 */
function su_enqueue_generator() {
	Su_Generator::enqueue_generator();
}

/**
 * Helper function to check that the given path is related to the current theme
 * or to the plugin directory.
 *
 * @since  5.4.0
 * @param  string $path Relative path to check.
 * @return bool         True if the given path relates to theme/plugin directory, False otherwise.
 */
function su_is_valid_template_name( $path ) {

	$path = su_set_file_extension( $path, 'php' );

	$allowed = apply_filters(
		'su/allowed_template_paths',
		array(
			get_stylesheet_directory(),
			get_template_directory(),
			plugin_dir_path( dirname( __FILE__ ) ),
		)
	);

	foreach ( $allowed as $dir ) {

		$dir  = untrailingslashit( $dir );
		$real = realpath( path_join( $dir, $path ) );

		$dir  = str_replace( '\\', '/', $dir );
		$real = str_replace( '\\', '/', $real );

		if ( strpos( $real, $dir ) === 0 ) {
			return true;
		}

	}

	return false;

}

/**
 * Helper function to add/remove file extension to/from a given path.
 *
 * @since  5.4.0
 * @param  string      $path      Path to add/remove file extension to/from.
 * @param  string|bool $extension Extension to add/remove.
 * @return string                 Modified file path.
 */
function su_set_file_extension( $path, $extension ) {

	$path_info = pathinfo( $path );

	if ( ! $extension ) {
		return path_join( $path_info['dirname'], $path_info['filename'] );
	}

	if ( empty( $path_info['extension'] ) || $path_info['extension'] !== $extension ) {
		$path .= ".{$extension}";
	}

	return $path;

}

/**
 * Helper function to add utm-args to an URL.
 *
 * @since 5.6.1
 */
function su_get_utm_link( $url, $utm ) {

	return add_query_arg(
		array(
			'utm_source'   => $utm[0],
			'utm_medium'   => $utm[1],
			'utm_campaign' => $utm[2],
		),
		$url
	);

}

/**
 * Helper function to check if a passed value is a positive number.
 *
 * Returns true for positive numbers, allows integers and strings.
 *
 * @param  mixed  $value Value to test
 * @return bool          True if passed value is a positive number (integer or string), False otherwise
 */
function su_is_positive_number( $value ) {

	if ( ! is_string( $value ) && ! is_int( $value ) ) {
		return false;
	}

	if ( ! ctype_digit( (string) $value ) ) {
		return false;
	}

	return (int) $value > 0;

}

/**
 * Helper function to join multiple path pieces into one.
 *
 * @return string Merged path pieces
 */
function su_join_paths() {

	$is_absolute = func_get_arg( 0 ) !== ltrim( func_get_arg( 0 ), '\\/' );

	$pieces = array_map(
		function( $piece ) {
			return trim( $piece, '\\/' );
		},
		func_get_args()
	);

	$path = implode( DIRECTORY_SEPARATOR, $pieces );

	if ( $is_absolute ) {
		$path = DIRECTORY_SEPARATOR . $path;
	}

	return $path;

}
