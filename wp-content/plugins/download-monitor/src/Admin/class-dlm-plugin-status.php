<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Plugin_Status class.
 *
 * @since 4.9.6
 */
class DLM_Plugin_Status {

	/**
	 * Holds the class object.
	 *
	 * @since 4.9.6
	 *
	 * @var object
	 */
	public static $instance;

	private function __construct() {
		$this->set_hooks();
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_Plugin_Status object.
	 * @since 4.9.6
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Plugin_Status ) ) {
			self::$instance = new DLM_Plugin_Status();
		}

		return self::$instance;
	}

	/**
	 * Set required hooks
	 *
	 * @since 4.9.6
	 */
	private function set_hooks() {
		// Add Templates tab in the Download Monitor's settings page.
		add_filter( 'dlm_settings', array( $this, 'status_tab' ), 15, 1 );
		// Add tests to the Site Health Info page.
		add_filter( 'site_status_tests', array( $this, 'add_wp_tests' ), 30, 1 );
		// Add required modules to the Site Health Info page.
		add_filter( 'site_status_test_php_modules', array( $this, 'check_modules' ), 30, 1 );
	}

	/**
	 * Add Status tab in the Download Monitor's settings page.
	 *
	 * @param  array  $settings  Array of settings.
	 *
	 * @return array
	 * @since 4.9.6
	 */
	public function status_tab( $settings ) {
		$settings['general']['sections']['templates'] = array(
			'title'  => __( 'Templates', 'download-monitor' ),
			'fields' => array(
				array(
					'name'     => '',
					'type'     => 'templates_table',
					'priority' => 10,
				),
			),
		);

		return $settings;
	}

	/**
	 * Get info on the current active theme, info on parent theme
	 * and a list of template overrides.
	 *
	 * @return array
	 *
	 * @since 4.9.6
	 */
	public function get_theme_info() {
		$theme_info = get_transient( 'dlm_templates_info' );

		//if ( false === $theme_info ) {
		$active_theme = wp_get_theme();

		// Get parent theme info if this theme is a child theme, otherwise
		// pass empty info in the response.
		if ( is_child_theme() ) {
			$parent_theme      = wp_get_theme( $active_theme->template );
			$parent_theme_info = array(
				'parent_name'           => $parent_theme->name,
				'parent_version'        => $parent_theme->version,
				'parent_version_latest' => self::get_latest_theme_version( $parent_theme ),
				'parent_author_url'     => $parent_theme->{'Author URI'},
			);
		} else {
			$parent_theme_info = array(
				'parent_name'           => '',
				'parent_version'        => '',
				'parent_version_latest' => '',
				'parent_author_url'     => '',
			);
		}

		/**
		 * Scan the theme directory for all DLM templates to see if our theme
		 * overrides any of them.
		 */
		$override_files     = array();
		$outdated_templates = false;
		/**
		 * Filter the list of template files to scan. Defaults to all files in the templates directory.
		 *
		 * @hook  dlm_template_files
		 *
		 * @param  array  $scan_files  Array of template files to scan.
		 *
		 * @since 4.9.6
		 */
		$scan_files = apply_filters( 'dlm_template_files', self::scan_template_files( plugin_dir_path( DLM_PLUGIN_FILE ) . '/templates' ) );

		foreach ( $scan_files as $file ) {
			$located = apply_filters( 'dlm_get_template', $file, $file, array(), 'download-monitor', $this->templates_path() );

			if ( file_exists( $located ) ) {
				$theme_file = $located;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . $this->templates_path() . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $this->templates_path() . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $this->templates_path() . $file ) ) {
				$theme_file = get_template_directory() . '/' . $this->templates_path() . $file;
			} else {
				$theme_file = false;
			}

			if ( ! empty( $theme_file ) ) {
				$core_file     = $file;
				$core_version  = self::get_file_version( plugin_dir_path( DLM_PLUGIN_FILE ) . '/templates/' . $core_file );
				$theme_version = self::get_file_version( $theme_file );
				if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
					if ( ! $outdated_templates ) {
						$outdated_templates = true;
					}
				}
				$override_files[] = array(
					'file'         => str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
					'version'      => $theme_version,
					'core_version' => $core_version,
				);
			}
		}

		$active_theme_info = array(
			'name'                   => $active_theme->name,
			'version'                => $active_theme->version,
			'template'               => $active_theme->template,
			'version_latest'         => self::get_latest_theme_version( $active_theme ),
			'author_url'             => esc_url_raw( $active_theme->{'Author URI'} ),
			'is_child_theme'         => is_child_theme(),
			'has_outdated_templates' => $outdated_templates,
			'overrides'              => $override_files,
		);

		$theme_info = array_merge( $active_theme_info, $parent_theme_info );
		set_transient( 'dlm_templates_info', $theme_info, HOUR_IN_SECONDS );

		//	}

		return $theme_info;
	}

	/**
	 * Retrieve metadata from a file. Based on WP Core's get_file_data function.
	 *
	 * @param  string  $file  Path to the file.
	 *
	 * @return string
	 * @since  4.9.6
	 */
	public static function get_file_version( $file ) {
		// Avoid notices if file does not exist.
		if ( ! file_exists( $file ) ) {
			return '';
		}

		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );       // @codingStandardsIgnoreLine.

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 ); // @codingStandardsIgnoreLine.

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );                   // @codingStandardsIgnoreLine.

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );
		$version   = '';

		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
			$version = _cleanup_header_comment( $match[1] );
		}

		return $version;
	}

	/**
	 * Scan the template files.
	 *
	 * @param  string  $template_path  Path to the template directory.
	 *
	 * @return array
	 * @since 4.9.6
	 */
	public static function scan_template_files( $template_path ) {
		$files  = @scandir( $template_path ); // @codingStandardsIgnoreLine.
		$result = array();

		if ( ! empty( $files ) ) {
			foreach ( $files as $key => $value ) {
				if ( ! in_array( $value, array( '.', '..' ), true ) ) {
					if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
						$sub_files = self::scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
						foreach ( $sub_files as $sub_file ) {
							$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
						}
					} else {
						$result[] = $value;
					}
				}
			}
		}

		return $result;
	}


	/**
	 * Get latest version of a theme by slug.
	 *
	 * @param  object  $theme  WP_Theme object.
	 *
	 * @return string Version number if found.
	 * @since 4.9.6
	 */
	public static function get_latest_theme_version( $theme ) {
		include_once ABSPATH . 'wp-admin/includes/theme.php';

		$api = themes_api(
			'theme_information',
			array(
				'slug'   => $theme->get_stylesheet(),
				'fields' => array(
					'sections' => false,
					'tags'     => false,
				),
			)
		);

		$update_theme_version = 0;

		// Check .org for updates.
		if ( is_object( $api ) && ! is_wp_error( $api ) && isset( $api->version ) ) {
			$update_theme_version = $api->version;
		}

		return $update_theme_version;
	}

	/**
	 * Get the path to the templates directory.
	 *
	 * @return string
	 * @since 4.9.6
	 */
	public function templates_path() {
		return apply_filters( 'dlm_template_path', 'download-monitor/' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	/**
	 * Add tests to the Site Health Info page.
	 *
	 * @param  array  $tests  Array of tests.
	 *
	 * @return array
	 * @since 4.9.6
	 */
	public function add_wp_tests( $tests ) {
		$tests['direct']['dlm_required_modules'] = array(
			'label' => __( 'Download Monitor required modules / functions', 'download-monitor' ),
			'test'  => array( $this, 'dlm_required_modules' ),
		);

		return $tests;
	}

	/**
	 * Check if the download meets the requirements to be downloaded.
	 *
	 * @return array
	 * @since 4.9.6
	 *
	 */
	public function dlm_required_functions() {
		$errors = $this->check_requirements();
		// Default good result.
		$result = array(
			'label'       => __( 'DLM - All required modules/functions are active!', 'download-monitor' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Plugin functionality', 'download-monitor' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Modules and functions help Download Monitor achieve the functionality you desire, and to do that we require that functions and modules, that Download Monitor depends on, be enabled.', 'download-monitor' )
			),
			'actions'     => '',
			'test'        => 'dlm_required_functions',
		);
		// Check if there are any errors.
		if ( ! empty( $errors ) ) {
			$result = array(
				'label'       => __( 'DLM - One or more functions are missing!', 'download-monitor' ),
				'status'      => 'critical',
				'badge'       => array(
					'label' => __( 'Plugin functionality', 'download-monitor' ),
					'color' => 'blue',
				),
				'description' => sprintf(
					'<p>%s</p>',
					__( 'Modules and functions help Download Monitor achieve the functionality you desire, and to do that we require that functions and modules, that Download Monitor depends on, be enabled.', 'download-monitor' )
				),
				'actions'     => sprintf(
					'<p>%s</p>',
					__( 'Ask your hosting service to enable the following required modules/functions!', 'download-monitor' )
				),
				'test'        => 'dlm_required_functions',
			);

			// Show functions errors.
			$result['actions'] .= '<strong>' . __( 'Functions:', 'download-monitor' ) . '</strong><ul>';
			foreach ( $errors as $function ) {
				$result['actions'] .= '<li>' . $function . '</li>';
			}
			$result['actions'] .= '</ul>';
		}

		return $result;
	}

	/**
	 * Check if the download meets the requirements to be downloaded.
	 *
	 *
	 * @return array
	 * @since 4.9.6
	 *
	 */
	private function check_requirements() {
		$errors = array();
		/**
		 * Filter the requirements to be checked. Will be completed with more requirements in the future if needed.
		 *
		 * @hook  dlm_health_check_requirements_functions
		 *
		 * @param  array  $checks  Array of requirements to be checked.
		 *
		 * @since 4.9.6
		 */
		$checks = apply_filters(
			'dlm_health_check_requirements_functions',
			array( 'set_time_limit', 'session_write_close', 'ini_set', 'error_reporting' )
		);
		// Let's do the checks for functions.
		if ( ! empty( $checks ) ) {
			foreach ( $checks as $function ) {
				if ( ! function_exists( $function ) ) {
					$errors[] = $function;
				}
			}
		}

		return $errors;
	}

	/**
	 * Check if all the required modules are active.
	 *
	 * @return array
	 * @since 4.9.6
	 *
	 */
	public function check_modules( $modules ) {
		// For the moment we only return the modules from WordPress. Placed here for future use.
		return $modules;
	}
}
