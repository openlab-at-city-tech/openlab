<?php


/**
 * Theme update check
 *
 * @return void
 * @since  1.0
 */

add_action( 'admin_init', 'typology_run_updater' );

if ( !function_exists( 'typology_run_updater' ) ):
	function typology_run_updater() {

		$user = typology_get_option( 'theme_update_username' );
		$apikey = typology_get_option( 'theme_update_apikey' );
		if ( !empty( $user ) && !empty( $apikey ) ) {
			include_once get_template_directory() .'/inc/updater/class-pixelentity-theme-update.php';
			PixelentityThemeUpdate::init( $user, $apikey );
		}
	}
endif;



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
	}
endif;


/**
 * Display welcome message and quick tips after theme activation
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_welcome_msg' ) ):
	function typology_welcome_msg() {
		if ( !get_option( 'typology_welcome_box_displayed' ) ) { 
			update_option( 'typology_theme_version', TYPOLOGY_THEME_VERSION );
			include_once get_template_directory() .'/core/admin/welcome-panel.php';
		}
	}
endif;


/**
 * Display message when new version of the theme is installed/updated
 *
 * @since  1.0
 */

if ( !function_exists( 'typology_update_msg' ) ):
	function typology_update_msg() {
		if ( get_option( 'typology_welcome_box_displayed' ) ) {
			$prev_version = get_option( 'typology_theme_version' );
			$cur_version = TYPOLOGY_THEME_VERSION;
			if ( $prev_version === false ) { $prev_version = '0.0.0'; }
			if ( version_compare( $cur_version, $prev_version, '>' ) ) {
				include_once get_template_directory() .'/core/admin/update-panel.php';
			}
		}
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


if ( !function_exists( 'typology_dashboard_widget_cb' ) ):
	function typology_dashboard_widget_cb() {
		$hide = false;
		if ( $data = get_transient( 'typology_mksaw' ) ) {
			if ( $data != 'error' ) {
				echo $data;
			} else {
				$hide = true;
			}
		} else {
			$url = 'https://demo.mekshq.com/mksaw.php';
			$args = array( 'body' => array( 'key' => md5( 'meks' ), 'theme' => 'typology' ) );
			$response = wp_remote_post( $url, $args );
			if ( !is_wp_error( $response ) ) {
				$json = wp_remote_retrieve_body( $response );
				if ( !empty( $json ) ) {
					$json = ( json_decode( $json ) );
					if ( isset( $json->data ) ) {
						echo $json->data;
						set_transient( 'typology_mksaw', $json->data, 86400 );
					} else {
						set_transient( 'typology_mksaw', 'error', 86400 );
						$hide = true;
					}
				} else {
					set_transient( 'typology_mksaw', 'error', 86400 );
					$hide = true;
				}

			} else {
				set_transient( 'typology_mksaw', 'error', 86400 );
				$hide = true;
			}
		}

		if ( $hide ) {
			echo '<style>#typology_dashboard_widget {display:none;}</style>'; //hide widget if data is not returned properly
		}

	}
endif;
?>