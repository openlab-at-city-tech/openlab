<?php

/**
 * The class responsible for plugin upgrade procedures.
 *
 * @since        5.0.0
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */
final class Shortcodes_Ultimate_Upgrade {

	/**
	 * The path to the main plugin file.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string    $plugin_file   The path to the main plugin file.
	 */
	private $plugin_file;

	/**
	 * The current version of the plugin.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string    $current_version   The current version of the plugin.
	 */
	private $current_version;

	/**
	 * Name of the option which stores plugin version.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string    $option_name   Name of the option which stores plugin version.
	 */
	private $option_name;

	/**
	 * The previous saved version.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      string    $saved_version   The previous saved version.
	 */
	private $saved_version;

	/**
	 * Define the functionality of the updater.
	 *
	 * @since   5.0.0
	 * @param string  $plugin_file    The path to the main plugin file.
	 * @param string  $plugin_version The current version of the plugin.
	 */
	public function __construct( $plugin_file, $plugin_version ) {

		$this->plugin_file     = $plugin_file;
		$this->current_version = $plugin_version;
		$this->option_name     = 'su_option_version';
		$this->saved_version   = get_option( $this->option_name, 0 );

	}

	/**
	 * Run upgrade procedures (if needed).
	 *
	 * @since  5.0.0
	 */
	public function maybe_upgrade() {

		if ( ! $this->is_version_changed() ) {
			return;
		}

		if ( $this->is_previous_version_less_than( '5.0.0' ) ) {
			$this->upgrade_to_5_0_0();
		}

		if ( $this->is_previous_version_less_than( '5.0.7' ) ) {
			$this->upgrade_to_5_0_7();
		}

		if ( $this->is_previous_version_less_than( '5.1.1' ) ) {
			$this->upgrade_to_5_1_1();
		}

		if ( $this->is_previous_version_less_than( '5.2.0' ) ) {
			$this->upgrade_to_5_2_0();
		}

		$this->save_current_version();

	}

	/**
	 * Conditional check if plugin was updated.
	 *
	 * @since  5.0.0
	 * @access private
	 * @return boolean True if plugin was updated, False otherwise.
	 */
	private function is_version_changed() {
		return $this->is_previous_version_less_than( $this->current_version );
	}

	/**
	 * Conditional check if previous version of the plugin less than passed one.
	 *
	 * @since  5.0.0
	 * @access private
	 * @return boolean True if previous version of the plugin less than passed one, False otherwise.
	 */
	private function is_previous_version_less_than( $version ) {
		return version_compare( $this->saved_version, $version, '<' );
	}

	/**
	 * Save current version number.
	 *
	 * @since  5.0.0
	 * @access private
	 */
	private function save_current_version() {
		update_option( $this->option_name, $this->current_version, false );
	}

	/**
	 * Upgrade the plugin to version 5.0.0
	 *
	 * 1. Replace 'su_vote' option with 'su_option_dismissed_notices'.
	 * 2. Delete 'su_installed' option.
	 * 3. Remove extra slashes from 'su_option_custom-css' option.
	 *
	 * @since   5.0.0
	 * @access  private
	 */
	private function upgrade_to_5_0_0() {

		/**
		 * 1. Replace 'su_vote' option with 'su_option_dismissed_notices'.
		 */
		if ( get_option( 'su_vote' ) ) {

			$dismissed_notices = get_option( 'su_option_dismissed_notices' );

			if ( ! is_array( $dismissed_notices ) ) {

				$dismissed_notices = array(
					'rate' => true
				);

			}

			update_option( 'su_option_dismissed_notices', $dismissed_notices );

			delete_option( 'su_vote' );

		}


		/**
		 * 2. Delete 'su_installed' option.
		 */
		delete_option( 'su_installed' );


		/**
		 * 3. Remove extra slashes from 'su_option_custom-css' option.
		 */
		$custom_css = get_option( 'su_option_custom-css' );

		if ( ! empty( $custom_css ) ) {

			$custom_css = stripslashes( $custom_css );

			update_option( 'su_option_custom-css', $custom_css, true );

		}

	}

	/**
	 * Upgrade the plugin to version 5.0.7
	 *
	 * 1. Rename `su_generator_access` option to `su_option_generator_access`.
	 *
	 * @since   5.0.0
	 * @access  private
	 */
	private function upgrade_to_5_0_7() {

		/**
		 * 1. Rename `su_generator_access` option to `su_option_generator_access`.
		 */
		$su_generator_access_value = get_option( 'su_generator_access' );

		if ( $su_generator_access_value ) {

			delete_option( 'su_generator_access' );

			add_option( 'su_option_generator_access', $su_generator_access_value, '', false );

		}

	}

	/**
	 * Upgrade the plugin to version 5.1.1
	 *
	 * 1. Add `su_option_supported_blocks` option.
	 *
	 * @since   5.1.1
	 * @access  private
	 */
	private function upgrade_to_5_1_1() {

		/**
		 * 1. Add `su_option_supported_blocks` option.
		 */
		$supported_blocks = 'su_option_supported_blocks';

		if ( false === get_option( $supported_blocks ) ) {
			add_option( $supported_blocks, array_keys( su_get_config( 'supported-blocks' ) ) );
		}

	}

	/**
	 * Upgrade the plugin to version 5.2.0
	 *
	 * 1. Add `su_option_generator_access` option.
	 * 2. Add `su_option_enable_shortcodes_in` option.
	 *
	 * @since   5.2.0
	 * @access  private
	 */
	private function upgrade_to_5_2_0() {

		/**
		 * 1. Add `su_option_generator_access` option.
		 */
		$generator_access = 'su_option_generator_access';

		if ( false === get_option( $generator_access ) ) {
			add_option( $generator_access, 'manage_options' );
		}

		/**
		 * 2. Add `su_option_enable_shortcodes_in` option.
		 */
		$enable_shortcodes_in = 'su_option_enable_shortcodes_in';

		if ( false === get_option( $enable_shortcodes_in ) ) {
			add_option( $enable_shortcodes_in, array( 'category_description', 'widget_text' ) );
		}

	}

}
