<?php
/**
 * Flush Local Fonts Cache Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Flush_Local_Fonts
 */
class Astra_Flush_Local_Fonts extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/flush-font-local';
		$this->category    = 'astra';
		$this->label       = __( 'Flush Astra Local Fonts Cache', 'astra' );
		$this->description = __( 'Flushes the local fonts cache and regenerates font files for the Astra theme.', 'astra' );
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
		return array();
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'flush local fonts cache',
			'clear local fonts cache',
			'regenerate local fonts',
			'reset local fonts cache',
			'flush fonts cache',
			'clear fonts cache',
			'regenerate font files',
			'reset fonts cache',
			'flush local font files',
			'clear local font files',
			'regenerate local font assets',
			'reset local font files',
			'flush cached fonts',
			'clear cached fonts',
			'regenerate fonts folder',
			'reset fonts folder',
			'flush google fonts cache',
			'clear google fonts cache',
			'regenerate google fonts',
			'reset google fonts files',
			'flush self hosted fonts',
			'clear self hosted fonts',
			'regenerate self hosted fonts',
			'reset self hosted fonts cache',
			'flush downloaded fonts',
			'clear downloaded fonts',
			'regenerate downloaded fonts',
			'reset downloaded fonts cache',
			'rebuild local fonts',
			'rebuild fonts cache',
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

		$load_locally_enabled = Astra_API_Init::get_admin_settings_option( 'self_hosted_gfonts', false );

		if ( ! $load_locally_enabled ) {
			return Astra_Abilities_Response::error(
				__( 'Cannot flush local fonts cache.', 'astra' ),
				__( 'Load Google Fonts Locally must be enabled first.', 'astra' )
			);
		}

		// Reuse the dashboard's flush: deletes the correct fonts folder and clears the cached font options.
		$flushed = astra_webfont_loader_instance( '' )->astra_delete_fonts_folder();

		if ( ! $flushed ) {
			return Astra_Abilities_Response::error(
				__( 'Failed to flush local fonts cache.', 'astra' ),
				__( 'Please try again later.', 'astra' )
			);
		}

		do_action( 'astra_regenerate_fonts_folder' );

		return Astra_Abilities_Response::success(
			__( 'Local fonts cache flushed successfully.', 'astra' ),
			array(
				'flushed' => true,
			)
		);
	}
}

Astra_Flush_Local_Fonts::register();
