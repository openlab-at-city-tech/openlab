<?php

// Before all hooks.
add_action( 'init', 'anr_plugin_update', -15 );

function anr_plugin_update() {
	$prev_version = anr_get_option( 'version', '3.1' );
	if ( version_compare( $prev_version, ANR_PLUGIN_VERSION, '!=' ) ) {
		do_action( 'anr_plugin_update', $prev_version );
		anr_update_option( 'version', ANR_PLUGIN_VERSION );
	}
}

add_action( 'anr_plugin_update', 'anr_plugin_update_32' );

function anr_plugin_update_32( $prev_version ) {
	if ( version_compare( $prev_version, '3.2', '<' ) ) {
		if ( anr_same_settings_for_all_sites() ) {
			$options = get_site_option( 'anr_admin_options' );
		} else {
			$options = get_option( 'anr_admin_options' );
		}
		if ( ! $options || ! is_array( $options ) ) {
			return;
		}
		$options['error_message'] = str_replace( __( '<strong>ERROR</strong>: ', 'advanced-nocaptcha-recaptcha' ), '', anr_get_option( 'error_message' ) );

		$enabled_forms = [];
		if ( ! empty( $options['login'] ) ) {
			$enabled_forms[] = 'login';
		}
		if ( ! empty( $options['registration'] ) ) {
			$enabled_forms[] = 'registration';
		}
		if ( ! empty( $options['ms_user_signup'] ) ) {
			$enabled_forms[] = 'ms_user_signup';
		}
		if ( ! empty( $options['lost_password'] ) ) {
			$enabled_forms[] = 'lost_password';
		}
		if ( ! empty( $options['reset_password'] ) ) {
			$enabled_forms[] = 'reset_password';
		}
		if ( ! empty( $options['comment'] ) ) {
			$enabled_forms[] = 'comment';
		}
		if ( ! empty( $options['bb_new'] ) ) {
			$enabled_forms[] = 'bbp_new';
		}
		if ( ! empty( $options['bb_reply'] ) ) {
			$enabled_forms[] = 'bbp_reply';
		}
		if ( ! empty( $options['wc_checkout'] ) ) {
			$enabled_forms[] = 'wc_checkout';
		}
		$options['enabled_forms'] = $enabled_forms;

		unset( $options['login'], $options['registration'], $options['ms_user_signup'], $options['lost_password'], $options['reset_password'], $options['comment'], $options['bb_new'], $options['bb_reply'], $options['wc_checkout'] );

		anr_update_option( $options );
	}
}

add_action( 'anr_plugin_update', 'anr_plugin_update_51' );

function anr_plugin_update_51( $prev_version ) {
	if ( version_compare( $prev_version, '5.1', '<' ) ) {
		$options = [];
		if ( 'invisible' === anr_get_option( 'size' ) ) {
			$options['size']            = 'normal';
			$options['captcha_version'] = 'v2_invisible';
		}

		anr_update_option( $options );
	}
}

function anr_get_option( $option, $default = '', $section = 'anr_admin_options' ) {

	if ( anr_same_settings_for_all_sites() ) {
		$options = get_site_option( $section );
	} else {
		$options = get_option( $section );
	}

	if ( isset( $options[ $option ] ) ) {
		$value      = $options[ $option ];
		$is_default = false;
	} else {
		$value      = $default;
		$is_default = true;
	}
	return apply_filters( 'anr_get_option', $value, $option, $default, $is_default );
}

function anr_update_option( $options, $value = '', $section = 'anr_admin_options' ) {

	if ( $options && ! is_array( $options ) ) {
		$options = array(
			$options => $value,
		);
	}
	if ( ! is_array( $options ) ) {
		return false;
	}
	if ( anr_same_settings_for_all_sites() ) {
		update_site_option( $section, wp_parse_args( $options, get_site_option( $section ) ) );
	} else {
		update_option( $section, wp_parse_args( $options, get_option( $section ) ) );
	}

	return true;
}

function anr_is_form_enabled( $form ) {
	if ( ! $form ) {
		return false;
	}
	$enabled_forms = anr_get_option( 'enabled_forms', array() );
	if ( ! is_array( $enabled_forms ) ) {
		return false;
	}
	return in_array( $form, $enabled_forms, true );
}

function anr_translation() {
	// SETUP TEXT DOMAIN FOR TRANSLATIONS
	load_plugin_textdomain( 'advanced-nocaptcha-recaptcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function anr_login_enqueue_scripts() {

	if ( ! anr_get_option( 'remove_css' ) && 'normal' === anr_get_option( 'size', 'normal' ) && 'v2_checkbox' === anr_get_option( 'captcha_version', 'v2_checkbox' ) ) {
		wp_enqueue_style( 'anr-login-style', ANR_PLUGIN_URL . 'assets/css/style.css' );
	}
}

function anr_include_require_files() {
	$fep_files = array(
		'main' => 'anr-captcha-class.php',
	);
	if ( is_admin() ) {
		$fep_files['settings'] = 'admin/settings.php';
	}

	$fep_files = apply_filters( 'anr_include_files', $fep_files );

	foreach ( $fep_files as $fep_file ) {
		require_once $fep_file;
	}
}
add_action( 'wp_footer', 'anr_wp_footer', 999 );
add_action( 'login_footer', 'anr_wp_footer', 999 );

function anr_wp_footer() {
	anr_captcha_class::init()->footer_script();
}

add_action(
	'anr_captcha_form_field', function() {
		anr_captcha_form_field( true );
	}
);
add_shortcode( 'anr-captcha', 'anr_captcha_form_field' );

function anr_captcha_form_field( $echo = false ) {
	if ( $echo ) {
		anr_captcha_class::init()->form_field();
	} else {
		return anr_captcha_class::init()->form_field_return();
	}

}

function anr_verify_captcha( $response = false ) {
	return anr_captcha_class::init()->verify( $response );
}

add_filter( 'shake_error_codes', 'anr_add_shake_error_codes' );

function anr_add_shake_error_codes( $shake_error_codes ) {
	$shake_error_codes[] = 'anr_error';

	return $shake_error_codes;
}

function anr_fs_uninstall_cleanup() {
	global $wpdb;

	$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = 'anr-post' LIMIT 1" );

	if ( $post_id ) {
		// There may have too many post meta. delete them first in one query.
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $post_id ) );
		
		wp_delete_post( $post_id, true );
	}
}

function anr_fs_support_forum_url( $wp_org_support_forum_url ) {
	return 'https://www.shamimsplugins.com/support/forum/advanced-nocaptcha-and-invisible-captcha-pro/';
}

function anr_recaptcha_domain(){
	$domain = anr_get_option( 'recaptcha_domain', 'google.com' );
	return apply_filters( 'anr_recaptcha_domain', $domain );
}

function anr_same_settings_for_all_sites(){
	// Makes sure the plugin is defined before trying to use it
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}
	return is_plugin_active_for_network( plugin_basename( ANR_PLUGIN_FILE ) );
}
