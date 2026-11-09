<?php
/**
 * Configuration Manager Class
 *
 * Handles plugin configuration and options management.
 *
 * @package Broken_Link_Checker
 * @author  W-Shadow
 * @since   0.1.0
 */

if ( ! class_exists( 'blcConfigurationManager' ) ) {

	/**
	 * Configuration Manager for Broken Link Checker
	 *
	 * Manages plugin options including loading, saving, and retrieving settings.
	 */
	class blcConfigurationManager {

		/**
		 * Key of the configuration manager.
		 *
		 * @var string
		 */
		private $manager_key;

		/**
		 * Name of the option in the database.
		 *
		 * @var string
		 */
		private $option_name;

		/**
		 * Current plugin options.
		 *
		 * @var array
		 */
		private $options;

		/**
		 * Default settings for the plugin.
		 *
		 * @var array
		 */
		private $defaults;

		/**
		 * Whether options have been modified since last load/save.
		 *
		 * @var bool
		 */
		private $modified = false;

		/**
		 * Options loaded from the database.
		 *
		 * @var array
		 */
		public $loaded_values;

		/**
		 * Whether options have been successfully loaded from the database.
		 *
		 * @var bool
		 */
		public $db_option_loaded = false;

		/**
		 * Constructor.
		 *
		 * @param string $manager_key      Key of the configuration manager.
		 * @param string $option_name      Name of the option to load.
		 * @param array  $default_settings Default settings array.
		 */
		public function __construct( $manager_key, $option_name = '', $default_settings = null ) {
			$this->manager_key = $manager_key;
			$this->option_name = $option_name;

			if ( is_array( $default_settings ) ) {
				$this->defaults = $default_settings;
			} else {
				$this->defaults = array();
			}
			$this->modified      = false;
			$this->loaded_values = array();
			$this->options       = array();
		}

		/**
		 * Get the key of the configuration manager.
		 *
		 * @return string The manager key.
		 */
		public function get_manager_key(): string {
			return $this->manager_key;
		}

		/**
		 * Ensure options are loaded from database (lazy loading).
		 *
		 * @return void
		 */
		private function ensure_loaded() {
			if ( ! $this->db_option_loaded && ! empty( $this->option_name ) ) {
				$this->load_options();
			}
		}

		/**
		 * Set default options.
		 *
		 * @param array|null $default_settings Default settings array.
		 * @return void
		 */
		public function set_defaults( $default_settings = null ) {
			if ( is_array( $default_settings ) ) {
				$this->defaults = $default_settings;
			} else {
				$this->defaults = array();
			}
			$this->ensure_loaded();
			$this->options = array_merge( $this->defaults, $this->loaded_values );

			do_action( 'blc_configuration_options_changed', $this->options, $this );
		}

		/**
		 * Load plugin options from the database.
		 *
		 * The current $options values are not affected if this function fails.
		 *
		 * @param string $option_name Optional. Name of the option to load.
		 * @return bool True if options were loaded, false otherwise.
		 */
		public function load_options( $option_name = '' ) {
			$this->db_option_loaded = false;

			if ( ! empty( $option_name ) ) {
				$this->option_name = $option_name;
			}

			if ( empty( $this->option_name ) ) {
				return false;
			}

			$new_options = get_option( $this->option_name );

			// Decode JSON (if applicable).
			if ( is_string( $new_options ) && ! empty( $new_options ) ) {
				$new_options = json_decode( $new_options, true );
			}

			if ( ! is_array( $new_options ) ) {
				return false;
			}

			$this->loaded_values    = $new_options;
			$this->options          = array_merge( $this->defaults, $this->loaded_values );
			$this->db_option_loaded = true;
			$this->modified         = false;
			return true;
		}

		/**
		 * Save default options to the database.
		 *
		 * @return void
		 */
		public function save_defaults() {
			$this->options  = $this->defaults;
			$this->modified = true;
			$this->save_options();
		}

		/**
		 * Save plugin options to the database.
		 *
		 * @param string $option_name Optional. Save the options under this name.
		 * @return bool True if settings were saved, false otherwise.
		 */
		public function save_options( $option_name = '' ) {
			if ( ! empty( $option_name ) ) {
				$this->option_name = $option_name;
			}

			if ( empty( $this->option_name ) ) {
				return false;
			}

			$this->modified = false;

			return update_option( $this->option_name, json_encode( $this->options ), 'no' );
		}

		/**
		 * Retrieve a specific setting.
		 *
		 * @param string $key     The option key to retrieve.
		 * @param mixed  $default Optional. Default value if key doesn't exist.
		 * @return mixed The option value or default.
		 */
		public function get( $key, $default = null ) {
			$this->ensure_loaded();

			if ( array_key_exists( $key, $this->options ) ) {
				return $this->options[ $key ];
			}

			return $default;
		}

		/**
		 * Update or add a setting.
		 *
		 * @param string $key   The option key to set.
		 * @param mixed  $value The value to set.
		 * @return void
		 */
		public function set( $key, $value ) {
			$this->ensure_loaded();

			$this->options[ $key ] = $value;
			$this->modified        = true;

			do_action( 'blc_configuration_options_changed', $this->options, $this );
		}

		/**
		 * Update multiple settings at once.
		 *
		 * Only updates settings that are declared in defaults or already loaded options.
		 *
		 * @param array $new_options Associative array of key-value pairs to update.
		 * @return void
		 */
		public function update_multiple( array $new_options ): void {
			$this->ensure_loaded();
			$allowed_keys = $this->get_available_settings();

			// Update only allowed options.
			foreach ( $new_options as $key => $value ) {
				if ( in_array( $key, $allowed_keys, true ) ) {
					$this->options[ $key ] = $value;
				}
			}

			$this->modified = true;
			do_action( 'blc_configuration_options_changed', $this->options, $this );
		}

		/**
		 * Check if a setting exists.
		 *
		 * @param string $key The option key to check.
		 * @return bool True if the key exists, false otherwise.
		 */
		public function has( $key ) {
			$this->ensure_loaded();
			return array_key_exists( $key, $this->options );
		}

		/**
		 * Check if options are empty.
		 *
		 * @return bool True if options are empty, false otherwise.
		 */
		public function is_empty() {
			$this->ensure_loaded();
			return empty( $this->options );
		}

		/**
		 * Get all options.
		 *
		 * @return array The options array.
		 */
		public function get_options(): array {
			$this->ensure_loaded();
			return $this->options;
		}

		/**
		 * Set all options.
		 *
		 * @param array $options The options array to set.
		 * @return void
		 */
		public function set_options( array $options ): void {
			$this->options  = $options;
			$this->modified = true;

			do_action( 'blc_configuration_options_changed', $this->options, $this );
		}

		/**
		 * Get a single option value.
		 *
		 * @param string $key     The option key to retrieve.
		 * @param mixed  $default Optional. Default value if key doesn't exist.
		 * @return mixed The option value or default.
		 */
		public function get_option( string $key, $default = null ) {
			return $this->get( $key, $default );
		}

		/**
		 * Set a single option value.
		 *
		 * @param string $key   The option key to set.
		 * @param mixed  $value The value to set.
		 * @return void
		 */
		public function set_option( string $key, $value ): void {
			$this->set( $key, $value );
			$this->modified = true;
		}

		/**
		 * Delete a single option.
		 *
		 * @param string $key The option key to delete.
		 * @return void
		 */
		public function delete_option( string $key ): void {
			$this->ensure_loaded();
			if ( array_key_exists( $key, $this->options ) ) {
				unset( $this->options[ $key ] );
				$this->modified = true;

				do_action( 'blc_configuration_option_deleted', $key, $this );
				do_action( 'blc_configuration_options_changed', $this->options, $this );
			}
		}

		/**
		 * Check if a specific option exists.
		 *
		 * @param string $key The option key to check.
		 * @return bool True if the option exists, false otherwise.
		 */
		public function has_option( string $key ): bool {
			$this->ensure_loaded();
			return array_key_exists( $key, $this->options );
		}

		/**
		 * Check if options have been modified since last load/save.
		 *
		 * @return bool True if modified, false otherwise.
		 */
		public function is_modified(): bool {
			return $this->modified;
		}

		/**
		 * Get a list of all available settings keys.
		 *
		 * @return array List of setting keys.
		 */
		public function get_available_settings(): array {
			if ( $this->options ) {
				return array_keys( $this->options );
			} else {
				return array_keys( $this->defaults );
			}
		}

		/**
		 * Add multiple new options.
		 *
		 * @param array $new_options Associative array of key-value pairs to add.
		 * @return void
		 */
		public function add_options( array $new_options ): void {
			$this->ensure_loaded();
			$this->options  = array_merge( $this->options, $new_options );
			$this->modified = true;

			do_action( 'blc_configuration_options_added', $new_options, $this );
			do_action( 'blc_configuration_options_changed', $this->options, $this );
		}

		/**
		 * Get the name of the option in the database.
		 *
		 * @return string The option name.
		 */
		public function get_option_name(): string {
			return $this->option_name;
		}
	}
}
