<?php

/**
 * Change customize link to lead to theme options instead of live customizer 
 *
 * @since  1.0
 */

add_filter( 'wp_prepare_themes_for_js', 'typology_change_customize_link' );

if ( !function_exists( 'typology_change_customize_link' ) ):
	function typology_change_customize_link( $themes ) {
		if ( array_key_exists( 'typology', $themes ) ) {
			$themes['typology']['actions']['customize'] = admin_url( 'admin.php?page=typology_options' );
		}
		return $themes;
	}
endif;

/**
 * 
 * Change default arguments of author widget plugin
 *
 * @since  1.0
 */

add_filter( 'mks_author_widget_modify_defaults', 'typology_author_widget_defaults' );

if ( !function_exists( 'typology_author_widget_defaults' ) ):
	function typology_author_widget_defaults( $defaults ) {
		$defaults['avatar_size'] = 100;
		$defaults['display_all_posts'] = 0;
		return $defaults;
	}
endif;



/**
 * Change default arguments of social widget plugin
 *
 * @since  1.0
 */

add_filter( 'mks_social_widget_modify_defaults', 'typology_social_widget_defaults' );

if ( !function_exists( 'typology_social_widget_defaults' ) ):
	function typology_social_widget_defaults( $defaults ) {
		$defaults['size'] = 65;
		return $defaults;
	}
endif;


/**
 * Display theme admin notices
 *
 * @since  1.0
 */

add_action( 'admin_init', 'typology_check_installation' );

if ( !function_exists( 'typology_check_installation' ) ):
	function typology_check_installation() {
		add_action( 'admin_notices', 'typology_welcome_msg', 1 );
		add_action( 'admin_notices', 'typology_update_msg', 1 );
		add_action( 'admin_notices', 'typology_required_plugins_msg', 1 );
	}
endif;


/**
 * Display welcome message and quick tips after theme activation
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_welcome_msg' ) ):
	function typology_welcome_msg() {
		
		if ( get_option( 'typology_welcome_box_displayed' ) ||  get_option( 'merlin_typology_completed' ) ) {
			return false;
		}
		
		update_option( 'typology_theme_version', TYPOLOGY_THEME_VERSION );
		include_once get_parent_theme_file_path( '/core/admin/welcome-panel.php' );
		
	}
endif;


/**
 * Display message when new version of the theme is installed/updated
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_update_msg' ) ):
	function typology_update_msg() {

		if ( !get_option( 'typology_welcome_box_displayed' ) &&  !get_option( 'merlin_typology_completed' ) ) {
			return false;
		}

		$prev_version = get_option( 'typology_theme_version' );
		$cur_version = TYPOLOGY_THEME_VERSION;
		if ( $prev_version === false ) { $prev_version = '0.0.0'; }
		
		if ( version_compare( $cur_version, $prev_version, '>' ) ) {
			include_once get_parent_theme_file_path( '/core/admin/update-panel.php' );
		}
		
	}
endif;


/**
 * Display message if required plugins are not installed and activated
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_required_plugins_msg' ) ):
	function typology_required_plugins_msg() {

		if ( !get_option( 'typology_welcome_box_displayed' ) && !get_option( 'merlin_typology_completed' ) ) {
			return false;
		}

		if ( !typology_is_redux_active() ) {
			$class = 'notice notice-error';
			$message = wp_kses_post( sprintf( __( 'Important: Redux Framework plugin is required to run your theme options panel. Please visit <a href="%s">recommended plugins page</a> to install it.', 'typology' ), admin_url( 'admin.php?page=install-required-plugins' ) ) );
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

	}
endif;



/**
 * Check for Additional CSS in Theme Options and transfer it to Customize -> Additional CSS
 *
 * @return void
 * @since  1.0.5
 */
add_action('admin_init','typology_patch_additional_css');

if ( !function_exists( 'typology_patch_additional_css' ) ) :
	function typology_patch_additional_css() {

		$additional_css = typology_get_option( 'additional_css' );

		if ( empty( $additional_css ) ) {
			return false;
		}
		
		global $typology_settings;

		$typology_settings = get_option( 'typology_settings' ); 

		$typology_settings['additional_css'] = '';

		update_option( 'typology_settings', $typology_settings ) ;

		$customize_css = wp_get_custom_css_post();
		
		if ( !empty( $customize_css ) && !is_wp_error( $customize_css ) ) {
			$additional_css .= $customize_css->post_content;
		}

		wp_update_custom_css_post($additional_css);
	}
endif;


/**
 * Filter for social share option fields
 *
 * @param array $args - Array of default fields
 * @return array
 * @since  1.5.5
 */
add_filter( 'meks_ess_modify_options_fields', 'typology_social_share_option_fields_filter' );

if ( !function_exists( 'typology_social_share_option_fields_filter' ) ):
	function typology_social_share_option_fields_filter( $args ) {
		
		unset( $args['location'] );
		unset( $args['post_type'] );
		unset( $args['label_share'] );

		return $args;
	}
endif;

/**
 * Patching for social share platforms for meks easy share plugin
 *
 * @return void
 * @since  1.5.5
 */
add_action('admin_init','typology_patch_social_share_platforms');

if ( !function_exists( 'typology_patch_social_share_platforms' ) ) :
	function typology_patch_social_share_platforms() {

		$social_platforms = typology_get_option( 'social_share' );

		if ( empty( $social_platforms ) ) {
			return false;
		}
		
		global $typology_settings;
		$typology_settings = get_option( 'typology_settings' ); 

		$typology_settings['social_share'] = '';
		update_option( 'typology_settings', $typology_settings );
		
		$new_platforms = array();

		foreach ( $social_platforms as $platform => $value ) {
			if ( $value == '1' ) {
				$new_platforms['platforms'][] = $platform;
			}
		}

		update_option( 'meks_ess_settings', $new_platforms );

	}
endif;


/**
 * Add Meks dashboard widget
 *
 * @since  1.0
 */

add_action( 'wp_dashboard_setup', 'typology_add_dashboard_widgets' );

if ( !function_exists( 'typology_add_dashboard_widgets' ) ):
	function typology_add_dashboard_widgets() {
		add_meta_box( 'typology_dashboard_widget', 'Meks - WordPress Themes & Plugins', 'typology_dashboard_widget_cb', 'dashboard', 'side', 'high' );
	}
endif;


/**
 * Meks dashboard widget
 *
 * @since  1.0
 */
if ( !function_exists( 'typology_dashboard_widget_cb' ) ):
	function typology_dashboard_widget_cb() {

		$transient = 'typology_mksaw';
		$hide = '<style>#typology_dashboard_widget{display:none;}</style>';

		$data = get_transient( $transient );
	
		if ( $data == 'error' ) {
			echo $hide;
			return;
		}

		if ( !empty( $data ) ) {
			echo $data;
			return;
		}

		$url = 'https://demo.mekshq.com/mksaw.php';
		$args = array( 'body' => array( 'key' => md5( 'meks' ), 'theme' => 'typology' ) );
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			set_transient( $transient, 'error', DAY_IN_SECONDS );
			echo $hide;
			return;
		}

		$json = wp_remote_retrieve_body( $response );

		if ( empty( $json ) ) {
			set_transient( $transient, 'error', DAY_IN_SECONDS );
			echo $hide;
			return;
		}

		$json = json_decode( $json );

		if ( !isset( $json->data ) ) {
			set_transient( $transient, 'error', DAY_IN_SECONDS );
			echo $hide;
			return;
		} 

		set_transient( $transient, $json->data, DAY_IN_SECONDS );
		echo $json->data;
		
	}
endif;