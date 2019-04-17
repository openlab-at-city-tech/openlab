<?php

/**
 * Display theme admin notices
 *
 * @since  1.0
 */

add_action( 'admin_init', 'johannes_check_installation' );

if ( !function_exists( 'johannes_check_installation' ) ):
	function johannes_check_installation() {
		add_action( 'admin_notices', 'johannes_welcome_msg', 1 );
		add_action( 'admin_notices', 'johannes_update_msg', 1 );
		add_action( 'admin_notices', 'johannes_required_plugins_msg', 30 );
	}
endif;


/**
 * Display welcome message and quick tips after theme activation
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_welcome_msg' ) ):
	function johannes_welcome_msg() {

		if ( get_option( 'johannes_welcome_box_displayed' ) ||  get_option( 'merlin_johannes_completed' ) ) {
			return false;
		}

		update_option( 'johannes_theme_version', JOHANNES_THEME_VERSION );
		include_once get_parent_theme_file_path( '/core/admin/welcome-panel.php' );
	}
endif;


/**
 * Display message when new version of the theme is installed/updated
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_update_msg' ) ):
	function johannes_update_msg() {

		if ( !get_option( 'johannes_welcome_box_displayed' ) && !get_option( 'merlin_johannes_completed' ) ) {
			return false;
		}

		$prev_version = get_option( 'johannes_theme_version', '0.0.0' );

		if ( version_compare( JOHANNES_THEME_VERSION, $prev_version, '>' ) ) {
			include_once get_parent_theme_file_path( '/core/admin/update-panel.php' );
		}

	}
endif;

/**
 * Display message if required plugins are not installed and activated
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_required_plugins_msg' ) ):
	function johannes_required_plugins_msg() {

		if ( !get_option( 'johannes_welcome_box_displayed' ) && !get_option( 'merlin_johannes_completed' ) ) {
			return false;
		}

		if ( !johannes_is_kirki_active() ) {
			$class = 'notice notice-error';
			$message = sprintf( __( 'Important: Kirki Toolkit plugin is required to run your theme options customizer panel. Please visit <a href="%s">recommended plugins page</a> to install it.', 'johannes' ), admin_url( 'admin.php?page=johannes-plugins' ) );
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

	}
endif;


/**
 * Add widget form options
 *
 * Add custom options to each widget
 *
 * @return void
 * @since  1.0
 */

add_action( 'in_widget_form', 'johannes_add_widget_form_options', 10, 3 );

if ( !function_exists( 'johannes_add_widget_form_options' ) ) :

	function johannes_add_widget_form_options(  $widget, $return, $instance ) {

		if ( !isset( $instance['johannes-bg'] ) ) {
			$instance['johannes-bg'] = 0;
		}

		$backgrounds = johannes_get_background_opts();

?>
		<p class="johannes-opt-bg">
			<label><?php esc_html_e( 'Background/style', 'johannes' ); ?>:</label><br/>
			<label><input type="radio" id="<?php echo esc_attr( $widget->get_field_id( 'johannes-bg' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'johannes-bg' ) ); ?>" value="0" <?php checked( $instance['johannes-bg'], 0 ); ?> />
				<?php esc_html_e( 'Inherit (from global widget color option)', 'johannes' ); ?>
				</label><br/>
			<?php foreach ( $backgrounds as $id => $title ): ?>
				<label><input type="radio" id="<?php echo esc_attr( $widget->get_field_id( 'johannes-bg' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'johannes-bg' ) ); ?>" value="<?php echo esc_attr( $id ); ?>" <?php checked( $instance['johannes-bg'], $id ); ?> />
				<?php echo esc_html( $title ); ?>
				</label><br/>
			<?php endforeach; ?>
			<small class="howto"><?php  esc_html_e( 'Optionally apply specific background to this widget', 'johannes' ); ?></small>
		</p>

	<?php

	}

endif;



/**
 * Save widget form options
 *
 * Save custom options to each widget
 *
 * @return void
 * @since  1.0
 */

add_filter( 'widget_update_callback', 'johannes_save_widget_form_options', 20, 4 );

if ( !function_exists( 'johannes_save_widget_form_options' ) ) :

	function johannes_save_widget_form_options( $instance, $new_instance, $old_instance, $object ) {

		$instance['johannes-bg'] = isset( $new_instance['johannes-bg'] ) ? $new_instance['johannes-bg'] : 0;

		return $instance;

	}

endif;


/**
 * Store registered sidebars and menus so we can use them inside theme options
 * before wp_registered_sidebars global is initialized
 *
 * @since  1.0
 */

add_action( 'admin_init', 'johannes_check_sidebars_and_menus' );

if ( !function_exists( 'johannes_check_sidebars_and_menus' ) ):
	function johannes_check_sidebars_and_menus() {
		global $wp_registered_sidebars;
		if ( !empty( $wp_registered_sidebars ) ) {
			update_option( 'johannes_registered_sidebars', $wp_registered_sidebars );
		}

		$registered_menus = get_registered_nav_menus();
		if ( !empty( $registered_menus ) ) {
			update_option( 'johannes_registered_menus', $registered_menus );
		}

	}
endif;


/**
 * Change default arguments of author widget plugin
 *
 * @since  1.0
 */

add_filter( 'mks_author_widget_modify_defaults', 'johannes_author_widget_defaults' );

if ( !function_exists( 'johannes_author_widget_defaults' ) ):
	function johannes_author_widget_defaults( $defaults ) {
		$defaults['title'] = '';
		$defaults['avatar_size'] = 100;
		return $defaults;
	}
endif;


/**
 * Change default arguments of flickr widget plugin
 *
 * @since  1.0
 */

add_filter( 'mks_flickr_widget_modify_defaults', 'johannes_flickr_widget_defaults' );

if ( !function_exists( 'johannes_flickr_widget_defaults' ) ):
	function johannes_flickr_widget_defaults( $defaults ) {

		$defaults['count'] = 9;
		$defaults['t_width'] = 76;
		$defaults['t_height'] = 76;

		return $defaults;
	}
endif;

/**
 * Change default arguments of social widget plugin
 *
 * @since  1.0
 */

add_filter( 'mks_social_widget_modify_defaults', 'johannes_social_widget_defaults' );

if ( !function_exists( 'johannes_social_widget_defaults' ) ):
	function johannes_social_widget_defaults( $defaults ) {

		$defaults['size'] = 40;

		return $defaults;
	}
endif;


/**
 * Add Meks dashboard widget
 *
 * @since  1.0
 */

add_action( 'wp_dashboard_setup', 'johannes_add_dashboard_widgets' );

if ( !function_exists( 'johannes_add_dashboard_widgets' ) ):
	function johannes_add_dashboard_widgets() {
		add_meta_box( 'johannes_dashboard_widget', 'Meks - WordPress Themes & Plugins', 'johannes_dashboard_widget_cb', 'dashboard', 'side', 'high' );
	}
endif;


/**
 * Meks dashboard widget
 *
 * @since  1.0
 */
if ( !function_exists( 'johannes_dashboard_widget_cb' ) ):
	function johannes_dashboard_widget_cb() {
		$hide = false;
		if ( $data = get_transient( 'johannes_mksaw' ) ) {
			if ( $data != 'error' ) {
				echo $data;
			} else {
				$hide = true;
			}
		} else {
			$url = 'https://demo.mekshq.com/mksaw.php';
			$args = array( 'body' => array( 'key' => md5( 'meks' ), 'theme' => 'johannes' ) );
			$response = wp_remote_post( $url, $args );
			if ( !is_wp_error( $response ) ) {
				$json = wp_remote_retrieve_body( $response );
				if ( !empty( $json ) ) {
					$json = json_decode( $json );
					if ( isset( $json->data ) ) {
						echo $json->data;
						set_transient( 'johannes_mksaw', $json->data, 86400 );
					} else {
						set_transient( 'johannes_mksaw', 'error', 86400 );
						$hide = true;
					}
				} else {
					set_transient( 'johannes_mksaw', 'error', 86400 );
					$hide = true;
				}

			} else {
				set_transient( 'johannes_mksaw', 'error', 86400 );
				$hide = true;
			}
		}

		if ( $hide ) {
			echo '<style>#johannes_dashboard_widget {display:none;}</style>'; //hide widget if data is not returned properly
		}

	}
endif;
?>