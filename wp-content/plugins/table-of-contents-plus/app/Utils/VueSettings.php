<?php
namespace AIOSEO\TableOfContents\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Vue Settings for the user.
 *
 * @since 1.0.0
 */
class VueSettings {
	/**
	 * The name to lookup the settings with.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $settingsName = '';

	/**
	 * The settings array.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * All the default settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $defaults = [
		'toggledRadio'    => [],
		'tablePagination' => []
	];

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $settings List of settings.
	 */
	public function __construct( $settings = '_aioseo_table_of_contents_settings' ) {
		$this->settingsName = $settings;

		$userMeta       = get_user_meta( get_current_user_id(), $settings, true );
		$this->settings = ! empty( $userMeta ) ? array_replace_recursive( $this->defaults, $userMeta ) : $this->defaults;
	}

	/**
	 * Retrieves all settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of settings.
	 */
	public function all() {
		return array_replace_recursive( $this->defaults, $this->settings );
	}

	/**
	 * Retrieve a setting or null if missing.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name      The name of the property that is missing on the class.
	 * @param  array  $arguments The arguments passed into the method.
	 * @return mixed             The value from the settings or default/null.
	 */
	public function __call( $name, $arguments = [] ) {
		$value = isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : ( ! empty( $arguments[0] ) ? $arguments[0] : $this->getDefault( $name ) );

		return $value;
	}

	/**
	 * Retrieve a setting or null if missing.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name The name of the property that is missing on the class.
	 * @return mixed        The value from the settings or default/null.
	 */
	public function __get( $name ) {
		$value = isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : $this->getDefault( $name );

		return $value;
	}

	/**
	 * Sets the settings value and saves to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name  The name of the settings.
	 * @param  mixed  $value The value to set.
	 * @return void
	 */
	public function __set( $name, $value ) {
		$this->settings[ $name ] = $value;

		$this->update();
	}

	/**
	 * Checks if an settings is set or returns null if not.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name The name of the settings.
	 * @return mixed        True or null.
	 */
	public function __isset( $name ) {
		return isset( $this->settings[ $name ] ) ? false === empty( $this->settings[ $name ] ) : null;
	}

	/**
	 * Unsets the settings value and saves to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name  The name of the settings.
	 * @return void
	 */
	public function __unset( $name ) {
		if ( ! isset( $this->settings[ $name ] ) ) {
			return;
		}

		unset( $this->settings[ $name ] );

		$this->update();
	}

	/**
	 * Gets the default value for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name The settings name.
	 * @return mixed        The default value.
	 */
	public function getDefault( $name ) {
		return isset( $this->defaults[ $name ] ) ? $this->defaults[ $name ] : null;
	}

	/**
	 * Updates the settings in the database.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update() {
		update_user_meta( get_current_user_id(), $this->settingsName, $this->settings );
	}
}