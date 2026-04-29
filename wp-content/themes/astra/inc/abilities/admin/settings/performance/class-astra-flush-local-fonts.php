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

		$fonts_folder = $this->get_fonts_folder();

		if ( $fonts_folder && is_dir( $fonts_folder ) ) {
			$this->delete_fonts_folder( $fonts_folder );
		}

		do_action( 'astra_regenerate_fonts_folder' );

		return Astra_Abilities_Response::success(
			__( 'Local fonts cache flushed successfully.', 'astra' ),
			array(
				'flushed' => true,
			)
		);
	}

	/**
	 * Get the fonts folder path.
	 *
	 * @return string Fonts folder path.
	 */
	private function get_fonts_folder() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/astra-webfonts/';
	}

	/**
	 * Recursively delete the fonts folder.
	 *
	 * @param string $folder Folder path to delete.
	 * @return void
	 */
	private function delete_fonts_folder( $folder ) {
		if ( ! is_dir( $folder ) ) {
			return;
		}

		$files = array_diff( scandir( $folder ), array( '.', '..' ) );

		foreach ( $files as $file ) {
			$path = $folder . $file;
			if ( is_dir( $path ) ) {
				$this->delete_fonts_folder( $path . '/' );
			} else {
				wp_delete_file( $path );
			}
		}

		rmdir( $folder );
	}
}

Astra_Flush_Local_Fonts::register();
