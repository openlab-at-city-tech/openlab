<?php
/**
 * Contains the list of the deprecated functions
 * @since 4.2.3
 */

/**
 * Adds information about deprecated functions to plugin settings
 * during its call
 * @see    cptch_display_deprecated_filter()
 * @param  string   $func   The function name
 * @return void
 */
if ( ! function_exists( 'cptch_detect_deprecated' ) ) {
	function cptch_detect_deprecated( $func ) {
		global $cptch_options;

		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );
		if ( empty( $cptch_options['deprecated_usage'] ) )
			$cptch_options['deprecated_usage'] = array();
		if ( ! in_array( $func, $cptch_options['deprecated_usage'] ) ) {
			$cptch_options['deprecated_usage'][] = $func;
			update_option( 'cptch_options', $cptch_options );
		}
	}
}

/**
 * Removes information about deprecated functions from plugin settings
 * after the click on the close cross in "deprecated function" message block
 * @see cptch_display_deprecated_function_message();
 */
if ( ! function_exists( 'cptch_remove_deprecated' ) ) {
	function cptch_remove_deprecated() {
		global $cptch_options;

		if ( empty( $cptch_options ) )
			$cptch_options = get_option( 'cptch_options' );

		if ( ! empty( $cptch_options['deprecated_usage'] ) ) {
			unset( $cptch_options['deprecated_usage'] );
			update_option( 'cptch_options', $cptch_options );
		}
	}
}

/**
 * Stores deprecated plugin options field to make an compatibility with old
 * Contact Form plugin versions and to prevent errors during the plugin actiovation
 * if the free plugin version is activated
 * @see    cptch_parse_options(), cptch_get_default_options()
 * @param  boolean  $get_keys_only
 * @return array
 */
if ( ! function_exists( 'cptch_get_not_removable_options' ) ) {
	function cptch_get_not_removable_options( $get_keys_only = true ) {
		$old_options = array(
			'cptch_label_form'         => '',
			'cptch_required_symbol'    => '*',
			'cptch_login_form'         => '1',
			'cptch_comments_form'      => '1',
			'cptch_register_form'      => '1',
			'cptch_lost_password_form' => '1',
			'cptch_contact_form'       => '0'
		);
		return $get_keys_only ? array_keys( $old_options ) : $old_options;
	}
}

if ( ! function_exists( 'cptch_display_deprecated_function_message' ) ) {
	function cptch_display_deprecated_function_message() {
		global $cptch_options, $cptch_plugin_info;

		if ( empty( $cptch_options ) )
			$cptch_options = is_network_admin() ? get_site_option( 'cptch_options' ) : get_option( 'cptch_options' );

		if ( empty( $cptch_options['deprecated_usage'] ) )
			return '';

		if( isset( $_GET['cptch_nonce'] ) &&  wp_verify_nonce( $_GET['cptch_nonce'], 'cptch_clean_deprecated' ) ) {
			cptch_remove_deprecated();
			return '';
		}

		$funcs = implode( ', ', $cptch_options['deprecated_usage'] );
		$link  = '<a href="http://support.bestwebsoft.com/hc/en-us/articles/202353439" target="_blank">' . __( 'instruction', 'captcha' ) . '</a>.';
		$url = add_query_arg(
			array(
				'cptch_clean_deprecated' => '1',
				'cptch_nonce'            => wp_create_nonce( 'cptch_clean_deprecated' )
			),
			( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
		);
		$close_link = "<a href=\"{$url}\" class=\"close_icon notice-dismiss\"></a>";
		$message = sprintf( __( "Functions from the %1s plugin are used on your site. These functions are deprecated ( %2s ) since version %4s. Please replace them according to the %3s. If you close this message it will appear in case if deprecated function would be called again only", 'captcha' ), $cptch_plugin_info['Name'], $funcs, $link, '4.2.3' );

		return
			"<style>
				.cptch_deprecated_error {
					position: relative;
				}
				.cptch_deprecated_error a {
					text-decoration: none;
				}
			</style>
			<div class=\"cptch_deprecated_error error\"><p>{$message}</p>{$close_link}</div>";
	}
}

if ( ! function_exists( 'cptch_display_deprecated_filter' ) ) {
	function cptch_display_deprecated_filter() {
		if( ! has_filter( 'cptch_forms_list' ) )
			return false;
		$func = sprintf( __( 'some call functions for the "%s" filter hook', 'captcha' ), 'cptch_forms_list' );
		cptch_detect_deprecated( $func ); ?>
		<tr valign="top">
			<th scope="row"><?php _e( 'Enable the CAPTCHA for', 'captcha' ); ?></th>
			<td>
				<fieldset>
					<?php echo apply_filters( 'cptch_forms_list', '' ); ?>
				<fieldset>
			</td>
		</tr>
	<?php }
} ?>