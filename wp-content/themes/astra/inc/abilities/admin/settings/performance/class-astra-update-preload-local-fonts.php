<?php
/**
 * Update Preload Local Fonts Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Preload_Local_Fonts
 */
class Astra_Update_Preload_Local_Fonts extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-font-preload-local';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Preload Local Fonts Status', 'astra' );
		$this->description = __( 'Updates the Preload Local Fonts setting for the Astra theme (enable or disable).', 'astra' );
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'write';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'enabled' => array(
					'type'        => 'boolean',
					'description' => 'Enable or disable Preload Local Fonts.',
				),
			),
			'required'   => array( 'enabled' ),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'enable preload local fonts',
			'disable preload local fonts',
			'turn on fonts preloading',
			'turn off fonts preloading',
			'activate preload local fonts',
			'deactivate preload local fonts',
			'enable fonts preload',
			'disable fonts preload',
			'turn on local fonts preload',
			'turn off local fonts preload',
			'enable fonts eager loading',
			'disable fonts eager loading',
			'activate fonts preloading',
			'deactivate fonts preloading',
			'enable fonts immediate load',
			'disable fonts immediate load',
			'turn on fonts priority loading',
			'turn off fonts priority loading',
			'enable preload fonts on page load',
			'disable preload fonts on page load',
			'activate fonts instant loading',
			'deactivate fonts instant loading',
			'enable fonts fast loading',
			'disable fonts fast loading',
			'turn on fonts quick load',
			'turn off fonts quick load',
			'enable fonts speed optimization',
			'disable fonts speed optimization',
			'activate fonts preload feature',
			'deactivate fonts preload feature',
			'make fonts load immediately',
			'stop fonts from preloading',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! defined( 'ASTRA_THEME_SETTINGS' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme is not active.', 'astra' ),
				__( 'Please activate the Astra theme to use this feature.', 'astra' )
			);
		}

		if ( ! class_exists( 'Astra_API_Init' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra API not available.', 'astra' ),
				__( 'Please ensure Astra theme is properly loaded.', 'astra' )
			);
		}

		if ( ! isset( $args['enabled'] ) ) {
			return Astra_Abilities_Response::error(
				__( 'Enabled status is required.', 'astra' ),
				__( 'Please provide enabled as true or false.', 'astra' )
			);
		}

		$enabled              = (bool) $args['enabled'];
		$load_locally_enabled = Astra_API_Init::get_admin_settings_option( 'self_hosted_gfonts', false );

		if ( $enabled && ! $load_locally_enabled ) {
			return Astra_Abilities_Response::error(
				__( 'Cannot enable Preload Local Fonts.', 'astra' ),
				__( 'Load Google Fonts Locally must be enabled first.', 'astra' )
			);
		}

		Astra_API_Init::update_admin_settings_option( 'preload_local_fonts', $enabled );

		/* translators: %s: enabled or disabled */
		$message = sprintf( __( 'Preload Local Fonts %s.', 'astra' ), $enabled ? 'enabled' : 'disabled' );

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
				'enabled' => $enabled,
			)
		);
	}
}

Astra_Update_Preload_Local_Fonts::register();
