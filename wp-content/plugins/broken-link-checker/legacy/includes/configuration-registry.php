<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Registry for BLC Configuration Manager instances
 * Singleton pattern implementation
 */
class blcConfigurationRegistry {
	/**
	 * Singleton instance
	 *
	 * @var blcConfigurationRegistry|null
	 */
	private static $instance = null;

	/**
	 * Configuration store
	 *
	 * @var blcConfigStore
	 */
	public $options;

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {
		// Initialize registry.
		$this->options = new blcConfigStore();
		add_action( 'blc_configuration_options_changed', array( $this, 'invalidate_cache' ), 15, 2 );
	}

	/**
	 * Private clone method to prevent cloning
	 */
	private function __clone() {
		// Prevent cloning.
	}

	/**
	 * Get singleton instance
	 *
	 * @return blcConfigurationRegistry
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Create and initialize the singleton instance
	 *
	 * @return blcConfigurationRegistry
	 */
	public static function create() {
		self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Check if the singleton instance has been initialized
	 *
	 * @return bool
	 */
	public static function initialized() {
		return ! is_null( self::$instance );
	}

	/**
	 * Register a configuration manager
	 *
	 * @param  blcConfigurationManager $manager Configuration manager instance.
	 * @return bool True on success, false if key already exists
	 */
	public function register( $manager ) {
		$key = $manager->get_manager_key();

		if ( $this->options->has_manager( $key ) ) {
			return false;
		}

		$this->options->add_manager( $key, $manager );
		return true;
	}

	/**
	 * Get a registered configuration manager
	 *
	 * @param string $key Unique identifier for the manager.
	 * @return ?blcConfigurationManager Manager instance or default instance if not found
	 */
	public function get_manager( $key ) {
		if ( $this->options->has_manager( $key ) ) {
			return $this->options->get_manager( $key );
		}

		return $this->options->get_default_manager();
	}

	/**
	 * Get setting by name.
	 *
	 * @param string $setting Setting name.
	 * @param mixed  $default_value Default value if setting not found.
	 * @return mixed|null
	 */
	public function get( $setting, $default_value = null ) {
		if ( isset( $this->options[ $setting ] ) ) {
			return $this->options[ $setting ];
		}

		return $default_value;
	}

	/**
	 * Set setting value.
	 *
	 * @param strign $setting Setting name.
	 * @param mixed  $value New value.
	 * @return void
	 */
	public function set( $setting, $value ) {
		$this->options[ $setting ] = $value;
	}

	/**
	 * Check if a manager is registered
	 *
	 * @param string $key Unique identifier for the manager.
	 * @return bool True if registered, false otherwise
	 */
	public function has_manager( $key ) {
		return $this->options->has_manager( $key );
	}

	/**
	 * Unregister a configuration manager
	 *
	 * @param string $key Unique identifier for the manager.
	 * @return bool True on success, false if not found
	 */
	public function unregister( $key ) {
		if ( ! $this->options->has_manager( $key ) ) {
			return false;
		}
		$this->options->remove_manager( $key );
		return true;
	}

	/**
	 * Get all registered managers
	 *
	 * @return array All registered configuration managers
	 */
	public function get_all() {
		return $this->options->get_managers();
	}

	/**
	 * Save options for all modified managers
	 *
	 * @return void
	 */
	public function save_options() {
		foreach ( $this->options->get_managers() as $manager ) {
			if (
				$manager instanceof blcConfigurationManager &&
				$manager->is_modified()
			) {
				$manager->save_options();
			}
		}
	}

	/**
	 * Save default options for all managers that haven't loaded options yet
	 *
	 * @return void
	 */
	public function save_defaults() {
		foreach ( $this->options->get_managers() as $manager ) {
			if (
				$manager instanceof blcConfigurationManager &&
				! $manager->db_option_loaded
			) {
				$manager->save_defaults();
			}
		}
	}

	/**
	 * Set the default configuration manager by key
	 *
	 * @param string $key The key of the manager to set as default.
	 * @return void
	 */
	public function set_default_manager( $key ) {
		$this->options->set_default_manager( $key );
	}

	/**
	 * Get list of missing options.
	 *
	 * @return array List of missing options.
	 */
	public function get_missing_options(): array {
		$missing  = array();
		$managers = $this->get_all();

		foreach ( $managers as $manager ) {
			$manager->load_options();

			if ( ! $manager->db_option_loaded ) {
				$missing[] = $manager->get_option_name();
			}
		}

		return $missing;
	}

	/**
	 * Load options for all configuration managers.
	 *
	 * @return void
	 */
	public function load_options(): void {
		foreach ( $this->get_all() as $manager ) {
			$manager->load_options();
		}
	}

	/**
	 * Invalidate cached settings when options change.
	 *
	 * @param array                        $options Changed options.
	 * @param blcConfigurationManager|null $manager Manager that changed options.
	 * @return void
	 */
	public function invalidate_cache( $options, $manager = null ): void {
		if ( is_null( $manager ) ) {
			return;
		}

		$all_settings = array_keys( $options );

		// Update cached settings for the modified manager.
		$this->options->cache_available_settings(
			$manager->get_manager_key(),
			$all_settings
		);
	}
}
