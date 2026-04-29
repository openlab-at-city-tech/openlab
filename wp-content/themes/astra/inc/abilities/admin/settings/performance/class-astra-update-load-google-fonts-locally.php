<?php
/**
 * Update Load Google Fonts Locally Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Load_Google_Fonts_Locally
 */
class Astra_Update_Load_Google_Fonts_Locally extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-font-google-local';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Load Google Fonts Locally Status', 'astra' );
		$this->description = __( 'Updates the Load Google Fonts Locally setting for the Astra theme (enable or disable).', 'astra' );
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
					'description' => 'Enable or disable Load Google Fonts Locally.',
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
			'enable load google fonts locally',
			'disable load google fonts locally',
			'turn on google fonts local loading',
			'turn off google fonts local loading',
			'activate self hosted google fonts',
			'deactivate self hosted google fonts',
			'enable local font loading',
			'disable local font loading',
			'turn on fonts self hosting',
			'turn off fonts self hosting',
			'enable google fonts download',
			'disable google fonts download',
			'activate local google fonts',
			'deactivate local google fonts',
			'enable fonts local storage',
			'disable fonts local storage',
			'turn on google fonts caching',
			'turn off google fonts caching',
			'enable GDPR compliant fonts',
			'disable GDPR compliant fonts',
			'activate fonts offline mode',
			'deactivate fonts offline mode',
			'enable server hosted fonts',
			'disable server hosted fonts',
			'turn on local font files',
			'turn off local font files',
			'enable google fonts on server',
			'disable google fonts on server',
			'activate fonts local hosting',
			'deactivate fonts local hosting',
			'make fonts load from server',
			'stop fonts loading from server',
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

		$enabled = (bool) $args['enabled'];

		Astra_API_Init::update_admin_settings_option( 'self_hosted_gfonts', $enabled );

		/* translators: %s: enabled or disabled */
		$message = sprintf( __( 'Load Google Fonts Locally %s.', 'astra' ), $enabled ? 'enabled' : 'disabled' );

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
				'enabled' => $enabled,
			)
		);
	}
}

Astra_Update_Load_Google_Fonts_Locally::register();
