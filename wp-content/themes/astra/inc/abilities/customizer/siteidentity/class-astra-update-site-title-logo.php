<?php
/**
 * Update Site Title and Logo Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Site_Title_Logo
 */
class Astra_Update_Site_Title_Logo extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-site-title-logo';
		$this->category    = 'astra';
		$this->label       = __( 'Update Site Title and Logo', 'astra' );
		$this->description = __( 'Updates the Astra theme site title, tagline, logo, and related visibility settings.', 'astra' );

		$this->meta = array(
			'tool_type' => 'write',
		);
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
				'site_title'            => array(
					'type'        => 'string',
					'description' => __( 'Site title text.', 'astra' ),
				),
				'tagline'               => array(
					'type'        => 'string',
					'description' => __( 'Site tagline/description.', 'astra' ),
				),
				'logo_url'              => array(
					'type'        => 'string',
					'description' => __( 'Logo image URL.', 'astra' ),
				),
				'logo_id'               => array(
					'type'        => 'integer',
					'description' => __( 'Logo attachment ID (preferred over URL).', 'astra' ),
				),
				'retina_logo_url'       => array(
					'type'        => 'string',
					'description' => __( 'Retina logo image URL.', 'astra' ),
				),
				'mobile_logo_url'       => array(
					'type'        => 'string',
					'description' => __( 'Mobile logo image URL.', 'astra' ),
				),
				'logo_width'            => array(
					'type'        => 'object',
					'description' => __( 'Logo width (responsive).', 'astra' ),
					'properties'  => array(
						'desktop'      => array(
							'type'        => 'number',
							'description' => __( 'Desktop width in px.', 'astra' ),
						),
						'tablet'       => array(
							'type'        => 'number',
							'description' => __( 'Tablet width in px.', 'astra' ),
						),
						'mobile'       => array(
							'type'        => 'number',
							'description' => __( 'Mobile width in px.', 'astra' ),
						),
						'desktop-unit' => array(
							'type'        => 'string',
							'description' => __( 'Desktop unit.', 'astra' ),
						),
						'tablet-unit'  => array(
							'type'        => 'string',
							'description' => __( 'Tablet unit.', 'astra' ),
						),
						'mobile-unit'  => array(
							'type'        => 'string',
							'description' => __( 'Mobile unit.', 'astra' ),
						),
					),
				),
				'display_site_title'    => array(
					'type'        => 'object',
					'description' => __( 'Site title visibility (responsive).', 'astra' ),
					'properties'  => array(
						'desktop' => array(
							'type'        => 'boolean',
							'description' => __( 'Show on desktop.', 'astra' ),
						),
						'tablet'  => array(
							'type'        => 'boolean',
							'description' => __( 'Show on tablet.', 'astra' ),
						),
						'mobile'  => array(
							'type'        => 'boolean',
							'description' => __( 'Show on mobile.', 'astra' ),
						),
					),
				),
				'display_tagline'       => array(
					'type'        => 'object',
					'description' => __( 'Tagline visibility (responsive).', 'astra' ),
					'properties'  => array(
						'desktop' => array(
							'type'        => 'boolean',
							'description' => __( 'Show on desktop.', 'astra' ),
						),
						'tablet'  => array(
							'type'        => 'boolean',
							'description' => __( 'Show on tablet.', 'astra' ),
						),
						'mobile'  => array(
							'type'        => 'boolean',
							'description' => __( 'Show on mobile.', 'astra' ),
						),
					),
				),
				'logo_title_inline'     => array(
					'type'        => 'boolean',
					'description' => __( 'Display logo and title inline.', 'astra' ),
				),
				'different_retina_logo' => array(
					'type'        => 'boolean',
					'description' => __( 'Use different retina logo.', 'astra' ),
				),
				'different_mobile_logo' => array(
					'type'        => 'boolean',
					'description' => __( 'Use different mobile logo.', 'astra' ),
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
			'update site title to "My Awesome Site"',
			'change site logo image',
			'set site title and tagline',
			'hide site title on mobile',
			'update logo width to 200px',
			'change website logo',
			'set new site logo',
			'update site branding',
			'change tagline text',
			'set site description',
			'update logo size for mobile',
			'change logo width for tablet',
			'set retina logo image',
			'update mobile header logo',
			'hide tagline on desktop',
			'show site title on all devices',
			'set logo and title inline',
			'update logo to 150px width',
			'change site name and description',
			'set custom logo image',
			'update header branding',
			'change site identity settings',
			'set logo dimensions',
			'update site title visibility',
			'set different logo for mobile',
			'update retina display logo',
			'change header logo size',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$updated = array();

		if ( isset( $args['site_title'] ) ) {
			update_option( 'blogname', sanitize_text_field( $args['site_title'] ) );
			$updated[] = 'site_title';
		}

		if ( isset( $args['tagline'] ) ) {
			update_option( 'blogdescription', sanitize_text_field( $args['tagline'] ) );
			$updated[] = 'tagline';
		}

		if ( isset( $args['logo_id'] ) ) {
			$logo_id = absint( $args['logo_id'] );
			if ( $logo_id > 0 ) {
				set_theme_mod( 'custom_logo', $logo_id );
				$updated[] = 'logo';
			}
		} elseif ( isset( $args['logo_url'] ) ) {
			$logo_url = esc_url_raw( $args['logo_url'] );
			if ( ! empty( $logo_url ) ) {
				$attachment_id = attachment_url_to_postid( $logo_url );
				if ( $attachment_id ) {
					set_theme_mod( 'custom_logo', $attachment_id );
					$updated[] = 'logo';
				}
			}
		}

		if ( isset( $args['retina_logo_url'] ) ) {
			astra_update_option( 'ast-header-retina-logo', esc_url_raw( $args['retina_logo_url'] ) );
			$updated[] = 'retina_logo';
		}

		if ( isset( $args['mobile_logo_url'] ) ) {
			astra_update_option( 'mobile-header-logo', esc_url_raw( $args['mobile_logo_url'] ) );
			$updated[] = 'mobile_logo';
		}

		if ( isset( $args['logo_width'] ) && is_array( $args['logo_width'] ) ) {
			$logo_width = Astra_Abilities_Helper::sanitize_responsive_typo( $args['logo_width'] );
			astra_update_option( 'ast-header-responsive-logo-width', $logo_width );
			$updated[] = 'logo_width';
		}

		if ( isset( $args['display_site_title'] ) && is_array( $args['display_site_title'] ) ) {
			$visibility = array();
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( isset( $args['display_site_title'][ $device ] ) && $args['display_site_title'][ $device ] ) {
					$visibility[] = $device;
				}
			}
			astra_update_option( 'display-site-title-responsive', $visibility );
			$updated[] = 'site_title_visibility';
		}

		if ( isset( $args['display_tagline'] ) && is_array( $args['display_tagline'] ) ) {
			$visibility = array();
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( isset( $args['display_tagline'][ $device ] ) && $args['display_tagline'][ $device ] ) {
					$visibility[] = $device;
				}
			}
			astra_update_option( 'display-site-tagline-responsive', $visibility );
			$updated[] = 'tagline_visibility';
		}

		if ( isset( $args['logo_title_inline'] ) ) {
			astra_update_option( 'logo-title-inline', (bool) $args['logo_title_inline'] );
			$updated[] = 'logo_title_inline';
		}

		if ( isset( $args['different_retina_logo'] ) ) {
			astra_update_option( 'different-retina-logo', (bool) $args['different_retina_logo'] );
			$updated[] = 'different_retina_logo';
		}

		if ( isset( $args['different_mobile_logo'] ) ) {
			astra_update_option( 'different-mobile-logo', (bool) $args['different_mobile_logo'] );
			$updated[] = 'different_mobile_logo';
		}

		if ( empty( $updated ) ) {
			return Astra_Abilities_Response::error(
				__( 'No valid parameters provided to update.', 'astra' )
			);
		}

		return Astra_Abilities_Response::success(
			/* translators: %s: comma-separated list of updated fields */
			sprintf( __( 'Site title and logo updated successfully: %s', 'astra' ), implode( ', ', $updated ) ),
			array(
				'updated'               => $updated,
				'site_title'            => get_option( 'blogname' ),
				'tagline'               => get_option( 'blogdescription' ),
				'logo_id'               => get_theme_mod( 'custom_logo' ),
				'retina_logo'           => astra_get_option( 'ast-header-retina-logo' ),
				'mobile_logo'           => astra_get_option( 'mobile-header-logo' ),
				'logo_width'            => astra_get_option( 'ast-header-responsive-logo-width' ),
				'display_site_title'    => astra_get_option( 'display-site-title-responsive' ),
				'display_tagline'       => astra_get_option( 'display-site-tagline-responsive' ),
				'logo_title_inline'     => astra_get_option( 'logo-title-inline' ),
				'different_retina_logo' => astra_get_option( 'different-retina-logo' ),
				'different_mobile_logo' => astra_get_option( 'different-mobile-logo' ),
			)
		);
	}
}

Astra_Update_Site_Title_Logo::register();
