<?php

include "common/admin.php";

class Meow_WPMC_Admin extends MeowApps_Admin {

	public function __construct( $prefix, $mainfile, $domain ) {
		parent::__construct( $prefix, $mainfile, $domain );
		add_action( 'admin_menu', array( $this, 'app_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'pre_update_option', array( $this, 'pre_update_option' ), 10, 3 );
	}

	/**
	 * Filters and performs validation for certain options
	 * @param mixed $value Option value
	 * @param string $option Option name
	 * @param mixed $old_value The current value of the option
	 * @return mixed The actual value to be stored
	 */
	function pre_update_option( $value, $option, $old_value ) {
		if ( strpos( $option, 'wpmc_' ) !== 0 ) return $value; // Never touch extraneous options
		$validated = $this->validate_option( $option, $value );
		if ( $validated instanceof WP_Error ) {
			// TODO: Show warning for invalid option value
			return $old_value;
		}
		return $validated;
	}

	/**
	 * Validates certain option values
	 * @param string $option Option name
	 * @param mixed $value Option value
	 * @return mixed|WP_Error Validated value if no problem
	 */
	function validate_option( $option, $value ) {
		switch ( $option ) {
		case 'wpmc_dirs_filter':
		case 'wpmc_files_filter':
			if ( $value && @preg_match( $value, '' ) === false ) return new WP_Error( 'invalid_option', __( "Invalid Regular-Expression", 'media-cleaner' ) );
			break;
		}
		return $value;
	}

	function admin_notices() {
		$mediasBuffer = get_option( 'wpmc_medias_buffer', null );
		$postsBuffer = get_option( 'wpmc_posts_buffer', null );
		$analysisBuffer = get_option( 'wpmc_analysis_buffer', null );
		$delay = get_option( 'wpmc_delay', null );

		if ( !is_numeric( $mediasBuffer ) || $mediasBuffer < 1 )
			update_option( 'wpmc_medias_buffer', 100 );
		if ( !is_numeric( $postsBuffer ) || $postsBuffer < 1 )
			update_option( 'wpmc_posts_buffer', 5 );
		if ( !is_numeric( $analysisBuffer ) || $analysisBuffer < 1 )
			update_option( 'wpmc_analysis_buffer', 100 );
		if ( !is_numeric( $delay ) )
			update_option( 'wpmc_delay', 100 );

		$reset = isset ( $_GET[ 'reset' ] ) ? $_GET[ 'reset' ] : 0;
		if ( $reset ) {
			wpmc_reset();
			echo "<div class='notice notice-error'><p>";
			_e( "The Media Cleaner's database has been deleted. It will be re-created automatically next time you visit the Media Cleaner Dashboard.", 'media-cleaner' );
			echo "</p></div>";
		}

		if ( !$this->is_registered() && get_option( 'wpmc_method', 'media' ) == 'files' ) {
			echo "<div class='notice notice-error'><p>";
			_e( "The Pro version is required to scan files. You can <a target='_blank' href='http://meowapps.com/plugin/media-cleaner'>get a serial for the Pro version here</a>.", 'media-cleaner' );
			echo "</p></div>";
		}
	}

	function common_url( $file ) {
		return trailingslashit( plugin_dir_url( __FILE__ ) ) . 'common/' . $file;
	}

	function app_menu() {

		// SUBMENU > Settings
		add_submenu_page( 'meowapps-main-menu', 'Media Cleaner', 'Media Cleaner', 'manage_options',
			'wpmc_settings-menu', array( $this, 'admin_settings' ) );

			// SUBMENU > Settings > Settings (Scanning)
			add_settings_section( 'wpmc_settings', null, null, 'wpmc_settings-menu' );
			add_settings_field( 'wpmc_method', __( 'Method', 'media-cleaner' ),
				array( $this, 'admin_method_callback' ),
				'wpmc_settings-menu', 'wpmc_settings' );
			add_settings_field( 'wpmc_content', __( 'Content', 'media-cleaner' ),
				array( $this, 'admin_content_callback' ),
				'wpmc_settings-menu', 'wpmc_settings' );
			add_settings_field( 'wpmc_media_library', __( 'Media Library', 'media-cleaner' ),
				array( $this, 'admin_media_library_callback' ),
				'wpmc_settings-menu', 'wpmc_settings' );
			add_settings_field( 'wpmc_live_content', __( 'Live Content<br />(Pro)', 'media-cleaner' ),
				array( $this, 'admin_live_content_callback' ),
				'wpmc_settings-menu', 'wpmc_settings' );
			add_settings_field( 'wpmc_debuglogs', __( 'Logs', 'media-cleaner' ),
				array( $this, 'admin_debuglogs_callback' ),
				'wpmc_settings-menu', 'wpmc_settings', array( __( 'Enable', 'media-cleaner' ) ) );

			// SUBMENU > Settings > Filters
			add_settings_section( 'wpmc_filters_settings', null, null, 'wpmc_filters_settings-menu' );
			add_settings_field( 'wpmc_images_only', __( 'Images Only', 'media-cleaner' ),
				array( $this, 'admin_images_only_callback' ),
				'wpmc_filters_settings-menu', 'wpmc_filters_settings' );
			add_settings_field( 'wpmc_thumbnails_only', __( 'Thumbnails Only', 'media-cleaner' ),
				array( $this, 'admin_thumbnails_only_callback' ),
				'wpmc_filters_settings-menu', 'wpmc_filters_settings' );

			add_settings_field(
				'wpmc_dirs_filter',
				__( 'Directories Filter', 'media-cleaner' ),
				array( $this, 'admin_dirs_filter_callback' ),
				'wpmc_filters_settings-menu',
				'wpmc_filters_settings'
			);

			add_settings_field(
				'wpmc_files_filter',
				__( 'Files Filter', 'media-cleaner' ),
				array( $this, 'admin_files_filter_callback' ),
				'wpmc_filters_settings-menu',
				'wpmc_filters_settings'
			);

			// SUBMENU > Settings > UI
			add_settings_section( 'wpmc_ui_settings', null, null, 'wpmc_ui_settings-menu' );
			add_settings_field( 'wpmc_hide_thumbnails', __( 'Thumbnails', 'media-cleaner' ),
				array( $this, 'admin_hide_thumbnails_callback' ),
				'wpmc_ui_settings-menu', 'wpmc_ui_settings' );
			add_settings_field( 'wpmc_hide_warning', __( 'Warning Message', 'media-cleaner' ),
				array( $this, 'admin_hide_warning_callback' ),
				'wpmc_ui_settings-menu', 'wpmc_ui_settings' );
			add_settings_field( 'wpmc_results_per_page', __( 'Results Per Page', 'media-cleaner' ),
				array( $this, 'admin_results_per_page' ),
				'wpmc_ui_settings-menu', 'wpmc_ui_settings' );

			// SUBMENU > Settings > Advanced
			add_settings_section( 'wpmc_advanced_settings', null, null, 'wpmc_advanced_settings-menu' );
			add_settings_field( 'wpmc_medias_buffer', __( 'Medias Buffer', 'media-cleaner' ),
				array( $this, 'admin_medias_buffer_callback' ),
				'wpmc_advanced_settings-menu', 'wpmc_advanced_settings' );
			add_settings_field( 'wpmc_posts_buffer', __( 'Posts Buffer', 'media-cleaner' ),
				array( $this, 'admin_posts_buffer_callback' ),
				'wpmc_advanced_settings-menu', 'wpmc_advanced_settings' );
			add_settings_field( 'wpmc_analysis_buffer', __( 'Analysis Buffer', 'media-cleaner' ),
				array( $this, 'admin_analysis_buffer_callback' ),
				'wpmc_advanced_settings-menu', 'wpmc_advanced_settings' );
			add_settings_field( 'wpmc_delay', __( 'Delay (in ms)', 'media-cleaner' ),
				array( $this, 'admin_delay_callback' ),
				'wpmc_advanced_settings-menu', 'wpmc_advanced_settings' );
			add_settings_field( 'wpmc_shortcodes_disabled', __( 'Shortcodes', 'media-cleaner' ),
				array( $this, 'admin_shortcodes_disabled_callback' ),
				'wpmc_advanced_settings-menu', 'wpmc_advanced_settings' );

		// SETTINGS
		register_setting( 'wpmc_settings', 'wpmc_method' );
		register_setting( 'wpmc_settings', 'wpmc_content' );
		register_setting( 'wpmc_settings', 'wpmc_live_content' );
		register_setting( 'wpmc_settings', 'wpmc_media_library' );
		register_setting( 'wpmc_settings', 'wpmc_debuglogs' );

		register_setting( 'wpmc_filters_settings', 'wpmc_images_only' );
		register_setting( 'wpmc_filters_settings', 'wpmc_thumbnails_only' );
		register_setting( 'wpmc_filters_settings', 'wpmc_dirs_filter' );
		register_setting( 'wpmc_filters_settings', 'wpmc_files_filter' );

		register_setting( 'wpmc_ui_settings', 'wpmc_hide_thumbnails' );
		register_setting( 'wpmc_ui_settings', 'wpmc_hide_warning' );
		register_setting( 'wpmc_ui_settings', 'wpmc_results_per_page' );

		register_setting( 'wpmc_advanced_settings', 'wpmc_medias_buffer' );
		register_setting( 'wpmc_advanced_settings', 'wpmc_posts_buffer' );
		register_setting( 'wpmc_advanced_settings', 'wpmc_analysis_buffer' );
		register_setting( 'wpmc_advanced_settings', 'wpmc_delay' );
		register_setting( 'wpmc_advanced_settings', 'wpmc_shortcodes_disabled' );
	}

	function admin_medias_buffer_callback( $args ) {
		$value = get_option( 'wpmc_medias_buffer', 100 );
		$html = '<input type="number" style="width: 100%;" id="wpmc_medias_buffer" name="wpmc_medias_buffer" value="' . $value . '" />';
		$html .= '<br /><span class="description">' . __( 'The number of media entries to read at a time. This is fast, so the value should be between 50 and 1000.', 'media-cleaner' ) . '</span>';
		echo $html;
	}

	function admin_posts_buffer_callback( $args ) {
		$value = get_option( 'wpmc_posts_buffer', 5 );
		$html = '<input type="number" style="width: 100%;" id="wpmc_posts_buffer" name="wpmc_posts_buffer" value="' . $value . '" />';
		$html .= '<br /><span class="description">' . __( 'The number of posts (and any other post types) to analyze at a time. This is the most intense part of the process. Recommended value is between 1 (slow server) and 20 (excellent server).', 'media-cleaner' ) . '</span>';
		echo $html;
	}

	function admin_analysis_buffer_callback( $args ) {
		$value = get_option( 'wpmc_analysis_buffer', 100 );
		$html = '<input type="number" style="width: 100%;" id="wpmc_analysis_buffer" name="wpmc_analysis_buffer" value="' . $value . '" />';
		$html .= '<br /><span class="description">' . __( 'The number of media entries or files to analyze at a time. This is the main part of the process, but is is much faster than analyzing each post. Recommended value is between 20 (slow server) and 1000 (excellent server).', 'media-cleaner' ) . '</span>';
		echo $html;
	}

	function admin_delay_callback( $args ) {
		$value = get_option( 'wpmc_delay', 100 );
		$html = '<input type="number" style="width: 100%;" id="wpmc_delay" name="wpmc_delay" value="' . $value . '" />';
		$html .= '<br /><span class="description">' . __( 'Time to wait between each request (in milliseconds). The overall process is intensive so this gives the chance to your server to chill out a bit. A very good server doesn\'t need it, but a slow/shared hosting might even reject requests if they are too fast and frequent. Recommended value is actually 0, 100 for safety, 2000 or 5000 if your hosting is kind of cheap.', 'media-cleaner' ) . '</span>';
		echo $html;
	}

	function admin_settings() {
		?>
		<div class="wrap wrap-media-cleaner">
			<?php
				echo $this->display_title( "Media Cleaner" );
			?>
			<div class="meow-section meow-group">
				<div class="meow-box meow-col meow-span_2_of_2">
					<h3><?php _e('How to use', 'media-cleaner' ); ?></h3>
					<div class="inside">
						<?php _e( "You can choose two kind of methods. Usually, users like to analyze their Media Library for images which are not in used (Media Library Method + Content Check), and then, their Filesystem for images which aren't registered in the Media Library (Filesystem Method + Media Library Check). Check the <a target=\"_blank\" href=\"https://meowapps.com/media-cleaner-tutorial/\">tutorial</a> for more information.", 'media-cleaner' ); ?>
						<p class="submit">
							<a class="button button-primary" href="upload.php?page=media-cleaner"><?php echo _e( "Access Media Cleaner Dashboard", 'media-cleaner' ); ?></a>
							<a id='wpmc_reset' href='?page=wpmc_settings-menu&reset=1' class='button button-red exclusive' style='margin-left: 5px;'><span style="top: 4px; position: relative; left: -5px;" class="dashicons dashicons-sos"></span><?php _e('Delete Cleaner DB', 'media-cleaner'); ?></a>
						</p>
					</div>
				</div>
			</div>

			<div class="meow-section meow-group">

				<div class="meow-col meow-span_1_of_2">

					<div class="meow-box">
						<h3><?php _e('Scanning', 'media-cleaner' ); ?></h3>
						<div class="inside">
							<form method="post" action="options.php">
							<?php settings_fields( 'wpmc_settings' ); ?>
							<?php do_settings_sections( 'wpmc_settings-menu' ); ?>
							<?php submit_button(); ?>
							</form>
						</div>
					</div>

					<div class="meow-box">
						<h3><?php _e('Filters', 'media-cleaner' ); ?></h3>
						<div class="inside">
							<form method="post" action="options.php">
							<?php settings_fields( 'wpmc_filters_settings' ); ?>
							<?php do_settings_sections( 'wpmc_filters_settings-menu' ); ?>
							<?php submit_button(); ?>
							</form>
						</div>

					</div>

				</div>

				<div class="meow-col meow-span_1_of_2">
					<?php $this->display_serialkey_box( "https://meowapps.com/plugin/media-cleaner/" ); ?>

					<div class="meow-box">
						<h3><?php _e('UI', 'media-cleaner' ); ?></h3>
						<div class="inside">
							<form method="post" action="options.php">
							<?php settings_fields( 'wpmc_ui_settings' ); ?>
							<?php do_settings_sections( 'wpmc_ui_settings-menu' ); ?>
							<?php submit_button(); ?>
							</form>
						</div>
					</div>

					<div class="meow-box">
						<h3><?php _e('Advanced', 'media-cleaner' ); ?></h3>
						<div class="inside">
							<form method="post" action="options.php">
							<?php settings_fields( 'wpmc_advanced_settings' ); ?>
							<?php do_settings_sections( 'wpmc_advanced_settings-menu' ); ?>
							<?php submit_button(); ?>
							</form>
						</div>
					</div>

					<!--
					<?php if ( get_option( 'wpmc_shortcode', false ) ): ?>
					<div class="meow-box">
						<h3>Shortcodes</h3>
						<div class="inside"><small>
							<p>Here are the shortcodes registered in your WordPress by your theme and other plugins.</p>
							<?php
								global $shortcode_tags;
								try {
									if ( is_array( $shortcode_tags ) ) {
										$my_shortcodes = array();
										foreach ( $shortcode_tags as $sc )
											if ( $sc != '__return_false' ) {
												if ( is_string( $sc ) )
													array_push( $my_shortcodes, str_replace( '_shortcode', '', (string)$sc ) );
											}
										$my_shortcodes = implode( ', ', $my_shortcodes );
									}
								}
								catch (Exception $e) {
									$my_shortcodes = "";
								}
								echo $my_shortcodes;
							?>
						</small></div>
					</div>
					<?php endif; ?>
					-->

				</div>

			</div>
		</div>
		<?php
	}



	/*
		OPTIONS CALLBACKS
	*/

	function admin_method_callback( $args ) {
		$value = get_option( 'wpmc_method', 'media' );
		$html = '<select id="wpmc_method" name="wpmc_method">
			<option ' . selected( 'media', $value, false ) . 'value="media">' . __( 'Media Library', 'media-cleaner' ) .'</option>
			<option ' . disabled( $this->is_registered(), false, false ) . ' ' . selected( 'files', $value, false ) . 'value="files">' . __( 'Filesystem (Pro)', 'media-cleaner' ) .'</option>
		</select><br /><small>' . __( 'Check the <a target="_blank" href="https://meowapps.com/media-cleaner-tutorial/">tutorial</a> for more information.', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_debuglogs_callback( $args ) {
		global $wpmc;
		$debuglogs = get_option( 'wpmc_debuglogs' );
		$clearlogs = isset ( $_GET[ 'clearlogs' ] ) ? $_GET[ 'clearlogs' ] : 0;
		if ( $clearlogs && file_exists( plugin_dir_path( __FILE__ ) . '/media-cleaner.log' ) ) {
			unlink( plugin_dir_path( __FILE__ ) . '/media-cleaner.log' );
		}
		$html = '<input type="checkbox" id="wpmc_debuglogs" name="wpmc_debuglogs" value="1" ' .
			checked( 1, $debuglogs, false ) . '/>';
		$html .= '<label for="wpmc_debuglogs"> '  . $args[0] . '</label><br>';
		$html .= '<small>' . __( 'Creates an internal log file, for debugging purposes.', 'media-cleaner' );
		if ( $debuglogs && !file_exists( plugin_dir_path( __FILE__ ) . '/media-cleaner.log' ) ) {
			if ( !$wpmc->log( "Testing the logging feature. It works!" ) ) {
				$html .= sprintf( __( '<br /><b>Cannot create the logging file. Logging will not work. The plugin as a whole might not be able to work neither.</b>', 'media-cleaner' ), plugin_dir_url( __FILE__ ) );
			}
		}
		if ( file_exists( plugin_dir_path( __FILE__ ) . '/media-cleaner.log' ) ) {
			$html .= sprintf(
				// translators: %s is a plugin directory url
				__( '<br />The <a target="_blank" href="%smedia-cleaner.log">log file</a> is available. You can also <a href="?page=wpmc_settings-menu&clearlogs=true">clear</a> it.', 'media-cleaner' ),
				plugin_dir_url( __FILE__ )
			);
		}
		$html .= '</small>';
		echo $html;
	}

	function admin_media_library_callback( $args ) {
		$value = get_option( 'wpmc_media_library', true );
		$html = '<input type="checkbox" id="wpmc_media_library" name="wpmc_media_library" value="1" ' .
			disabled( get_option( 'wpmc_method', 'media' ) == 'files', false, false ) . ' ' .
			checked( 1, $value, false ) . '/>';
		$html .= '<label>' . __( 'Check', 'media-cleaner' ) . '</label><br /><small>' . __( 'Checks if the file is linked to a media. <i>Only matters to the Filesystem Method.</i>', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_content_callback( $args ) {
		$value = get_option( 'wpmc_content', true );
		$html = '<input type="checkbox" id="wpmc_content" name="wpmc_content" value="1" ' .
			checked( 1, $value, false ) . '/>';
		$html .= '<label>' . __( 'Check', 'media-cleaner' ) . '</label><br /><small>' . __( 'Check if the media/file is used in the content, such as Posts, Pages (and other Post Types), Metadata, Widgets, etc.', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_live_content_callback( $args ) {
		$value = get_option( 'wpmc_live_content', false );
		$html = '<input ' . disabled( $this->is_registered(), false, false ) .
			' type="checkbox" id="wpmc_content" name="wpmc_live_content" value="1" ' .
			checked( 1, $value, false ) . '/>';
		$html .= '<label>' . __( 'Check', 'media-cleaner' ) . '</label><br /><small>' . __( 'The live version of the website will be also analyzed (as if a visitor was loading it). <i>This will increase the accuracy of the results.</i>', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_shortcodes_disabled_callback( $args ) {
		$value = get_option( 'wpmc_shortcodes_disabled', null );
		$html = '<input type="checkbox" id="wpmc_shortcodes_disabled" name="wpmc_shortcodes_disabled" value="1" ' .
			checked( 1, get_option( 'wpmc_shortcodes_disabled' ), false ) . '/>';
		$html .= '<label>' . __( 'Disable Analysis', 'media-cleaner' ) . '</label><br /><small>' . __( 'Resolving shortcodes increase accuracy, but makes the process slower and takes more memory.', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_hide_thumbnails_callback( $args ) {
		$value = get_option( 'wpmc_hide_thumbnails', null );
		$html = '<input type="checkbox" id="wpmc_hide_thumbnails" name="wpmc_hide_thumbnails" value="1" ' .
			checked( 1, get_option( 'wpmc_hide_thumbnails' ), false ) . '/>';
		$html .= '<label>' . __( 'Hide', 'media-cleaner' ) . '</label><br /><small>' . __( 'If you prefer not to see the thumbnails.', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_hide_warning_callback( $args ) {
		$value = get_option( 'wpmc_hide_warning', null );
		$html = '<input type="checkbox" id="wpmc_hide_warning" name="wpmc_hide_warning" value="1" ' .
			checked( 1, get_option( 'wpmc_hide_warning' ), false ) . '/>';
		$html .= '<label>' . __( 'Hide', 'media-cleaner' ) . '</label><br /><small>' . __( 'Have you read it twice? If yes, hide it :)', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_results_per_page( $args ) {
		$value = get_option( 'wpmc_results_per_page', 20 );
		$html = <<< HTML
<input step="1" min="1" max="999" name="wpmc_results_per_page" id="wpmc_results_per_page" maxlength="3" value="{$value}" type="number">
HTML;
		echo $html;
	}

	function admin_images_only_callback( $args ) {
		$html = '<input type="checkbox" id="wpmc_images_only" name="wpmc_images_only" value="1" ' .
			disabled( get_option( 'wpmc_method', 'media' ) == 'media', false, false ) . ' ' .
			checked( 1, get_option( 'wpmc_images_only' ), false ) . '/>';
		$html .= '<label>' . __( 'Enable', 'media-cleaner' ) . '</label><br /><small>' . __( 'Restrict the Media Library scan to images. Therefore, no documents or anything else will be scanned.', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_thumbnails_only_callback( $args ) {
		$html = '<input type="checkbox" id="wpmc_thumbnails_only" name="wpmc_thumbnails_only" value="1" ' .
			disabled( get_option( 'wpmc_method', 'media' ) == 'files', false, false ) . ' ' .
			checked( 1, get_option( 'wpmc_thumbnails_only' ), false ) . '/>';
		$html .= '<label>' . __( 'Enable', 'media-cleaner' ) . '</label><br /><small>' . __( 'Restrict the Filesystem scan to thumbnails (files containing the resolution). If none of the checks above are selected, you will get the list of all the images and be able to remove them.', 'media-cleaner' ) . '</small>';
		echo $html;
	}

	function admin_dirs_filter_callback( $args ) {
		$value = get_option( 'wpmc_dirs_filter', '' );
		$invalid = @preg_match( $value, '' ) === false;
		?>
<input type="text" id="wpmc_dirs_filter" name="wpmc_dirs_filter" value="<?php echo $value; ?>" placeholder="/regex/" autocomplete="off" data-needs-validation style="font-family: monospace;">
<?php
	}

	function admin_files_filter_callback( $args ) {
		$value = get_option( 'wpmc_files_filter', '' );
		$invalid = @preg_match( $value, '' ) === false;
		?>
<input type="text" id="wpmc_files_filter" name="wpmc_files_filter" value="<?php echo $value; ?>" placeholder="/regex/" autocomplete="off" data-needs-validation style="font-family: monospace;">
<?php
	}

	/**
	 *
	 * GET / SET OPTIONS (TO REMOVE)
	 *
	 */

	function old_getoption( $option, $section, $default = '' ) {
		$options = get_option( $section );
		if ( isset( $options[$option] ) ) {
					if ( $options[$option] == "off" ) {
							return false;
					}
					if ( $options[$option] == "on" ) {
							return true;
					}
			return $options[$option];
			}
		return $default;
	}

}

?>
