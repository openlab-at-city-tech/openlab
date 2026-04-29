<?php
/**
 * Register customizer panels & sections for Easy Digital Downloads.
 *
 * @package     Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Customizer_Register_Edd_Section' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Customizer_Register_Edd_Section extends Astra_Customizer_Config_Base {
		/**
		 * Register Panels and Sections for Customizer.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.5.5
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$configs = array(
				/**
				 * WooCommerce
				 */
				array(
					'name'     => 'section-edd-group',
					'type'     => 'section',
					'title'    => __( 'Easy Digital Downloads', 'astra' ),
					'priority' => 60,
				),

				array(
					'name'     => 'section-edd-general',
					'title'    => __( 'General', 'astra' ),
					'type'     => 'section',
					'section'  => 'section-edd-group',
					'priority' => 10,
				),

				array(
					'name'     => 'section-edd-archive',
					'title'    => __( 'Product Archive', 'astra' ),
					'type'     => 'section',
					'section'  => 'section-edd-group',
					'priority' => 10,
				),

				array(
					'name'     => 'section-edd-single',
					'type'     => 'section',
					'title'    => __( 'Single Product', 'astra' ),
					'section'  => 'section-edd-group',
					'priority' => 15,
				),
			);

			// Upgrade nudge if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {
				$configs[] = self::get_upgrade_nudge_config(
					'ast-edd-group-pro-items',
					'section-edd-group',
					array( 'divider' => array() )
				);
			}

			return array_merge( $configurations, $configs );
		}

		/**
		 * Build the EDD "upgrade to Pro" nudge config shared across EDD sections.
		 *
		 * @param string $name    Setting name suffix used inside ASTRA_THEME_SETTINGS[...].
		 * @param string $section Customizer section the nudge belongs to.
		 * @param array  $args    Optional overrides. Supported keys: 'divider'. Pass an empty array
		 *                        for 'divider' to omit it.
		 * @return array Nudge configuration ready to be registered with the customizer.
		 * @since 4.13.0
		 */
		public static function get_upgrade_nudge_config( $name, $section, $args = array() ) {
			return array_merge(
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[' . $name . ']',
					'type'        => 'control',
					'control'     => 'ast-upgrade',
					'campaign'    => 'edd',
					'section'     => $section,
					'default'     => '',
					'priority'    => 999,
					'title'       => __( 'Selling Digital Products?', 'astra' ),
					'description' => __( 'Optimize your store for revenue growth with Business Toolkit!', 'astra' ),
					'choices'     => array(
						'one'   => array(
							'title' => __( 'Pre-designed EDD templates', 'astra' ),
						),
						'two'   => array(
							'title' => __( 'Automate sales with OttoKit', 'astra' ),
						),
						'three' => array(
							'title' => __( 'High-converting checkout', 'astra' ),
						),
						'four'  => array(
							'title' => __( 'Advanced design control', 'astra' ),
						),
						'five'  => array(
							'title' => __( 'Better product pages', 'astra' ),
						),
					),
					'divider' => array( 'ast_class' => 'ast-top-section-divider' ),
				),
				$args
			);
		}
	}
}

new Astra_Customizer_Register_Edd_Section();
