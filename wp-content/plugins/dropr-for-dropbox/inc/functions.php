<?php
/**
 * Dropdown Builder
 *
 * @param  String Dropdown name
 * @param  Array  Dropdown options array
 * @param  String Default selected value
 * @param  String Dropdown class name
 * @return String Dropdown html
 * @since   1.0
 */
function dropr_selectbuilder( $name, $options, $selected = '', $class = '', $setting = '' ) {
	if ( is_array( $options ) ) :
		echo "<select name=\"$name\" id=\"$name\" class=\"$class\" data-setting=\"$setting\">";
		foreach ( $options as $key => $option ) {
			echo "<option value=\"$key\"";
			if ( ! empty( $helptext ) ) {
				echo " title=\"$helptext\"";
			}
			if ( $key == $selected ) {
				echo ' selected="selected"';
			}
			echo ">$option</option>\n";
		}
		echo '</select>';
	endif;
}

/**
 * Human Readable filesize
 *
 * @since   1.0
 * @return  Human readable file size
 * @note    Replaces old gde_sanitizeOpts function
 */
function dropr_hrfilesize( $bytes, $decimals = 2 ) {
	$size   = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
	$factor = floor( ( strlen( $bytes ) - 1 ) / 3 );
	return sprintf( "%.{$decimals}f ", $bytes / pow( 1024, $factor ) ) . @$size[ $factor ];
}
/**
 * Sanitize dimensions (width, height)
 *
 * @since   1.0
 * @param   string $dim Dimension
 * @return  string Sanitized dimensions, or false if value is invalid
 * @note    Replaces old gde_sanitizeOpts function
 */

function dropr_sanitize( $dim ) {

	// remove any spacing junk
	$dim = trim( str_replace( ' ', '', $dim ) );

	if ( ! strstr( $dim, '%' ) ) {
		$type = 'px';
		$dim  = preg_replace( '/[^0-9]*/', '', $dim );
	} else {
		$type = '%';
		$dim  = preg_replace( '/[^0-9]*/', '', $dim );
		if ( (int) $dim > 100 ) {
			$dim = '100';
		}
	}

	if ( $dim ) {
		return $dim . $type;
	} else {
		return false;
	}
}
/**
 * Get default option values
 *
 * @since   1.0
 * @return  Array Default Options
 */
function dropr_defaults() {
	$defaults = array(
		'btntxt'      => 'Download',
		'fontsize'    => 16,
		'btntxtcolor' => '#ffffff',
		'vpadding'    => 13,
		'hpadding'    => 31,
		'bgcolor'     => '#2b92c2',
		'brthick'     => 2,
		'brradius'    => 5,
		'brcolor'     => '#007aa6',
	);
	return $defaults;
}
/**
 * Get option values
 *
 * @since   1.0
 * @return  Array Default Options
 */
function dropr_getoptions() {
	$defaults      = dropr_defaults();
	$wpdropoptions = get_option( 'dropr-settings' );
	$options       = wp_parse_args( $wpdropoptions, $defaults );
	return $options;
}

if ( ! function_exists( 'dropr_get_generic_options' ) ) {
	/**
	 * Get generic options.
	 *
	 * @since 1.3.0
	 * @return array
	 */
	function dropr_get_generic_options() {
		$defaults = array(
			'media_library_storage'  => 'dropbox',
			'featured_image_storage' => 'local',
		);
		$options  = get_option( 'dropr-generic-settings' );
		$options  = wp_parse_args( $options, $defaults );
		return $options;
	}
}

