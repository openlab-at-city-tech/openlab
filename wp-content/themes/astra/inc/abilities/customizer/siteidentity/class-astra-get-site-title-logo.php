<?php
/**
 * Get Site Title and Logo Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Site_Title_Logo
 */
class Astra_Get_Site_Title_Logo extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-site-title-logo';
		$this->label       = __( 'Get Site Title and Logo', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme site title, tagline, logo, and related visibility settings.', 'astra' );
		$this->category    = 'astra';
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
	 * Get output schema.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->build_output_schema(
			array(
				'site_title'            => array(
					'type'        => 'string',
					'description' => __( 'Current site title.', 'astra' ),
				),
				'tagline'               => array(
					'type'        => 'string',
					'description' => __( 'Current site tagline/description.', 'astra' ),
				),
				'logo_id'               => array(
					'type'        => 'integer',
					'description' => __( 'Logo attachment ID.', 'astra' ),
				),
				'logo_url'              => array(
					'type'        => 'string',
					'description' => __( 'Logo image URL.', 'astra' ),
				),
				'retina_logo'           => array(
					'type'        => 'string',
					'description' => __( 'Retina logo URL.', 'astra' ),
				),
				'mobile_logo'           => array(
					'type'        => 'string',
					'description' => __( 'Mobile logo URL.', 'astra' ),
				),
				'logo_width'            => array(
					'type'        => 'object',
					'description' => __( 'Responsive logo width settings.', 'astra' ),
				),
				'display_site_title'    => array(
					'type'        => 'object',
					'description' => __( 'Site title visibility per device.', 'astra' ),
				),
				'display_tagline'       => array(
					'type'        => 'object',
					'description' => __( 'Tagline visibility per device.', 'astra' ),
				),
				'logo_title_inline'     => array(
					'type'        => 'boolean',
					'description' => __( 'Whether logo and title are displayed inline.', 'astra' ),
				),
				'different_retina_logo' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether a different retina logo is enabled.', 'astra' ),
				),
				'different_mobile_logo' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether a different mobile logo is enabled.', 'astra' ),
				),
			)
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'get current site title',
			'show site logo settings',
			'view site branding configuration',
			'display site title and tagline',
			'get logo settings',
			'show current site identity',
			'view header branding',
			'display logo and title details',
			'get site name and description',
			'show logo dimensions',
			'view site title visibility',
			'display tagline settings',
			'get logo URL and size',
			'show retina logo configuration',
			'view mobile logo settings',
			'display logo width settings',
			'get site identity configuration',
			'show logo inline settings',
			'view site branding details',
			'display header logo info',
			'get custom logo settings',
			'show site title display settings',
			'view tagline visibility',
			'display logo responsive settings',
			'get complete site identity',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$site_title = get_option( 'blogname', '' );
		$tagline    = get_option( 'blogdescription', '' );
		$logo_id    = get_theme_mod( 'custom_logo', 0 );

		// Get logo URL from ID.
		$logo_url = '';
		if ( $logo_id ) {
			$logo_image = wp_get_attachment_image_src( $logo_id, 'full' );
			if ( $logo_image ) {
				$logo_url = $logo_image[0];
			}
		}

		return Astra_Abilities_Response::success(
			__( 'Retrieved site title and logo settings successfully.', 'astra' ),
			array(
				'site_title'            => $site_title,
				'tagline'               => $tagline,
				'logo_id'               => $logo_id,
				'logo_url'              => $logo_url,
				'retina_logo'           => astra_get_option( 'ast-header-retina-logo', '' ),
				'mobile_logo'           => astra_get_option( 'mobile-header-logo', '' ),
				'logo_width'            => astra_get_option( 'ast-header-responsive-logo-width', array() ),
				'display_site_title'    => astra_get_option( 'display-site-title-responsive', array() ),
				'display_tagline'       => astra_get_option( 'display-site-tagline-responsive', array() ),
				'logo_title_inline'     => astra_get_option( 'logo-title-inline', false ),
				'different_retina_logo' => astra_get_option( 'different-retina-logo', false ),
				'different_mobile_logo' => astra_get_option( 'different-mobile-logo', false ),
			)
		);
	}
}

Astra_Get_Site_Title_Logo::register();
