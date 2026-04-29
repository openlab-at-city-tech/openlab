<?php
/**
 * Migrate Header Components Ability
 *
 * Fixes incorrect component IDs in the header builder by normalizing them to Astra's expected format.
 * Useful for fixing issues where components like 'menu_secondary' need to be changed to 'menu-2'.
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Migrate_Header_Components
 */
class Astra_Migrate_Header_Components extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-header-component';
		$this->category    = 'astra';
		$this->label       = __( 'Migrate Astra Header Component IDs', 'astra' );
		$this->description = __( 'Scans and fixes incorrect header builder component IDs. Automatically converts variations like menu_secondary to menu-2, social_1 to social-icons-1, etc. Use this when header components are not showing in customizer/frontend due to incorrect IDs.', 'astra' );

		$this->meta = array(
			'tool_type'   => 'write',
			'constraints' => array(
				'usage_hints' => array(
					'when_to_use'    => 'Use this when header components appear in database but not in customizer/frontend. Common issue: menu_secondary instead of menu-2.',
					'what_it_fixes'  => 'Normalizes all component IDs to Astra format: menu-1, menu-2, social-icons-1, button-1, html-1, widget-1, etc.',
					'automatic'      => 'Automatically scans both desktop and mobile layouts and fixes all incorrect IDs in one operation.',
					'safe_operation' => 'Only changes IDs that need normalization. Does not modify layout structure or remove components.',
					'cache_clear'    => 'After migration, customizer cache is automatically cleared to show changes immediately.',
				),
			),
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
				'dry_run' => array(
					'type'        => 'boolean',
					'description' => 'If true, shows what would be changed without actually updating. Default: false',
					'default'     => false,
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
			'fix header component IDs',
			'migrate header components',
			'normalize header widget IDs',
			'fix menu_secondary to menu-2',
			'repair header builder components',
			'convert header component IDs',
			'fix header components not showing',
			'migrate header builder IDs',
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

		$dry_run = isset( $args['dry_run'] ) ? (bool) $args['dry_run'] : false;

		$theme_options = get_option( ASTRA_THEME_SETTINGS, array() );
		if ( ! is_array( $theme_options ) ) {
			$theme_options = array();
		}

		$changes     = array();
		$total_fixes = 0;

		// Migrate desktop header items.
		if ( isset( $theme_options['header-desktop-items'] ) && is_array( $theme_options['header-desktop-items'] ) ) {
			$desktop_result = $this->migrate_layout( $theme_options['header-desktop-items'], 'desktop' );
			if ( ! empty( $desktop_result['changed'] ) ) {
				$changes['desktop'] = isset( $desktop_result['changes'] ) ? $desktop_result['changes'] : array();
				$total_fixes       += isset( $desktop_result['count'] ) ? (int) $desktop_result['count'] : 0;
				if ( ! $dry_run && isset( $desktop_result['layout'] ) ) {
					$theme_options['header-desktop-items'] = $desktop_result['layout'];
				}
			}
		}

		// Migrate mobile header items.
		if ( isset( $theme_options['header-mobile-items'] ) && is_array( $theme_options['header-mobile-items'] ) ) {
			$mobile_result = $this->migrate_layout( $theme_options['header-mobile-items'], 'mobile' );
			if ( ! empty( $mobile_result['changed'] ) ) {
				$changes['mobile'] = isset( $mobile_result['changes'] ) ? $mobile_result['changes'] : array();
				$total_fixes      += isset( $mobile_result['count'] ) ? (int) $mobile_result['count'] : 0;
				if ( ! $dry_run && isset( $mobile_result['layout'] ) ) {
					$theme_options['header-mobile-items'] = $mobile_result['layout'];
				}
			}
		}

		if ( 0 === $total_fixes ) {
			return Astra_Abilities_Response::success(
				__( 'No component IDs need migration. All component IDs are already correct.', 'astra' ),
				array(
					'fixes_needed' => 0,
					'dry_run'      => $dry_run,
				)
			);
		}

		if ( ! $dry_run ) {
			update_option( ASTRA_THEME_SETTINGS, $theme_options );

			// Clear customizer cache.
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
			}

			$message = sprintf(
				/* translators: %d: number of fixes */
				_n(
					'Successfully migrated %d component ID. Changes are now visible in customizer and frontend.',
					'Successfully migrated %d component IDs. Changes are now visible in customizer and frontend.',
					$total_fixes,
					'astra'
				),
				$total_fixes
			);
		} else {
			$message = sprintf(
				/* translators: %d: number of fixes */
				_n(
					'Found %d component ID that needs migration. Run without dry_run to apply changes.',
					'Found %d component IDs that need migration. Run without dry_run to apply changes.',
					$total_fixes,
					'astra'
				),
				$total_fixes
			);
		}

		return Astra_Abilities_Response::success(
			$message,
			array(
				'fixes_applied' => ! $dry_run,
				'total_fixes'   => $total_fixes,
				'changes'       => $changes,
				'dry_run'       => $dry_run,
			)
		);
	}

	/**
	 * Migrate a header layout by normalizing component IDs.
	 *
	 * @param array  $layout Layout array.
	 * @param string $type   Layout type (desktop or mobile).
	 * @return array Migration result with changed status, count, changes, and migrated layout.
	 */
	private function migrate_layout( $layout, $type ) {
		$sections = array( 'popup', 'above', 'primary', 'below' );
		$changes  = array();
		$count    = 0;
		$changed  = false;

		foreach ( $sections as $section ) {
			if ( ! isset( $layout[ $section ] ) || ! is_array( $layout[ $section ] ) ) {
				continue;
			}

			foreach ( $layout[ $section ] as $zone => $components ) {
				if ( ! is_array( $components ) ) {
					continue;
				}

				$normalized_components = array();
				foreach ( $components as $component_id ) {
					$normalized_id = $this->normalize_component_id( $component_id );

					if ( $normalized_id !== $component_id ) {
						$changes[] = array(
							'section' => $section,
							'zone'    => $zone,
							'old_id'  => $component_id,
							'new_id'  => $normalized_id,
						);
						++$count;
						$changed = true;
					}

					$normalized_components[] = $normalized_id;
				}

				$layout[ $section ][ $zone ] = $normalized_components;
			}
		}

		return array(
			'changed' => $changed,
			'count'   => $count,
			'changes' => $changes,
			'layout'  => $layout,
		);
	}

	/**
	 * Normalize component ID to match Astra's expected format.
	 *
	 * @param string $component_id Component ID to normalize.
	 * @return string Normalized component ID.
	 */
	private function normalize_component_id( $component_id ) {
		$component_id = sanitize_text_field( $component_id );

		$id_mapping = array(
			// Menu variations.
			'menu_primary'      => 'menu-1',
			'primary_menu'      => 'menu-1',
			'primary-menu'      => 'menu-1',
			'menu_1'            => 'menu-1',
			'menu_secondary'    => 'menu-2',
			'secondary_menu'    => 'menu-2',
			'secondary-menu'    => 'menu-2',
			'menu_2'            => 'menu-2',
			// Social icons variations.
			'social_icons_1'    => 'social-icons-1',
			'social_1'          => 'social-icons-1',
			'social-1'          => 'social-icons-1',
			'social_icons_2'    => 'social-icons-2',
			'social_2'          => 'social-icons-2',
			'social-2'          => 'social-icons-2',
			// Button variations.
			'button_1'          => 'button-1',
			'button_2'          => 'button-2',
			'button_3'          => 'button-3',
			// HTML variations.
			'html_1'            => 'html-1',
			'html_2'            => 'html-2',
			'html_3'            => 'html-3',
			// Widget variations.
			'widget_1'          => 'widget-1',
			'widget_2'          => 'widget-2',
			'widget_3'          => 'widget-3',
			// Mobile variations.
			'mobile_menu'       => 'mobile-menu',
			'mobile_trigger'    => 'mobile-trigger',
			// WooCommerce variations.
			'woo_cart'          => 'woo-cart',
			'woocommerce_cart'  => 'woo-cart',
			// EDD variations.
			'edd_cart'          => 'edd-cart',
			// Other variations.
			'language_switcher' => 'language-switcher',
		);

		if ( isset( $id_mapping[ $component_id ] ) ) {
			return $id_mapping[ $component_id ];
		}

		return $component_id;
	}
}

Astra_Migrate_Header_Components::register();
