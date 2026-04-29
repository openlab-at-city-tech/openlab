<?php
/**
 * Update Performance Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Performance
 */
class Astra_Update_Performance extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-performance';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Performance Settings', 'astra' );
		$this->description = __( 'Updates performance settings for the Astra theme including font loading options.', 'astra' );
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
				'load_google_fonts_locally' => array(
					'type'        => 'boolean',
					'description' => 'Enable or disable Load Google Fonts Locally.',
				),
				'preload_local_fonts'       => array(
					'type'        => 'boolean',
					'description' => 'Enable or disable Preload Local Fonts.',
				),
			),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'update performance settings',
			'configure performance options',
			'set performance preferences',
			'change performance configuration',
			'update performance layout',
			'set font loading options',
			'configure font optimization',
			'update font performance',
			'change font loading settings',
			'set fonts to load locally',
			'enable local fonts with preload',
			'configure fonts self hosting',
			'update fonts loading configuration',
			'set google fonts to local',
			'enable fonts preloading',
			'configure performance optimization',
			'update all performance settings',
			'change performance preferences',
			'set performance and font options',
			'update fonts hosting settings',
			'configure local fonts loading',
			'set fonts performance options',
			'enable performance features',
			'update font caching settings',
			'configure fonts download options',
			'set self hosted fonts',
			'enable GDPR compliant fonts',
			'update fonts storage settings',
			'configure offline fonts',
			'set performance customization',
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

		$updated         = false;
		$update_messages = array();

		if ( isset( $args['load_google_fonts_locally'] ) ) {
			$load_locally = (bool) $args['load_google_fonts_locally'];
			Astra_API_Init::update_admin_settings_option( 'self_hosted_gfonts', $load_locally );
			$updated           = true;
			$update_messages[] = sprintf( 'Load Google Fonts Locally %s', $load_locally ? 'enabled' : 'disabled' );

			if ( ! $load_locally && isset( $args['preload_local_fonts'] ) && $args['preload_local_fonts'] ) {
				return Astra_Abilities_Response::error(
					__( 'Cannot enable Preload Local Fonts when Load Google Fonts Locally is disabled.', 'astra' ),
					__( 'Please enable Load Google Fonts Locally first or set preload_local_fonts to false.', 'astra' )
				);
			}
		}

		if ( isset( $args['preload_local_fonts'] ) ) {
			$preload              = (bool) $args['preload_local_fonts'];
			$load_locally_enabled = Astra_API_Init::get_admin_settings_option( 'self_hosted_gfonts', false );

			if ( $preload && ! $load_locally_enabled ) {
				return Astra_Abilities_Response::error(
					__( 'Cannot enable Preload Local Fonts.', 'astra' ),
					__( 'Load Google Fonts Locally must be enabled first.', 'astra' )
				);
			}

			Astra_API_Init::update_admin_settings_option( 'preload_local_fonts', $preload );
			$updated           = true;
			$update_messages[] = sprintf( 'Preload Local Fonts %s', $preload ? 'enabled' : 'disabled' );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one setting to update.', 'astra' )
			);
		}

		$message = 'Performance settings updated: ' . implode( ', ', $update_messages ) . '.';

		return Astra_Abilities_Response::success(
			$message,
			array( 'updated' => true )
		);
	}
}

Astra_Update_Performance::register();
