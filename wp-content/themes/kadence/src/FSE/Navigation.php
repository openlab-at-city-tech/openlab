<?php
/**
 * Kadence\FSE\Navigation class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Classic_To_Block_Menu_Converter;
use WP_Term;

/**
 * Gives the header the site's own menus in Full Site Editing mode.
 *
 * Core's fallback for a Navigation block with no menu set is the most recently published
 * navigation menu, which can be one the site never used, such as a starter template's
 * list of pages. So when the mode turns on, the menus at the Primary and Mobile locations
 * become navigation menus, and the header and its overlay point at them.
 *
 * @since 1.6.0
 */
final class Navigation {

	/**
	 * Option that stores the navigation menu ID for each menu location.
	 *
	 * @since 1.6.0
	 */
	private const OPTION = 'kadence_fse_navigation';

	/**
	 * Option that stores the navigation menu ID the theme converted from each classic menu, by menu ID.
	 *
	 * @since 1.6.0
	 */
	private const COPIES_OPTION = 'kadence_fse_navigation_copies';

	/**
	 * Menu locations the header uses, Primary last so that on a first conversion its copy is
	 * the newest navigation menu, which core's fallback picks for a Navigation block with no
	 * menu set.
	 *
	 * @since 1.6.0
	 */
	private const LOCATIONS = [ 'mobile', 'primary' ];

	/**
	 * Turns the menus at the header's locations into navigation menus.
	 *
	 * A classic menu the theme converted before keeps its copy, so turning the mode on
	 * again keeps edits made in the Site Editor. Navigation menus the theme didn't make
	 * are never used, even when they share the menu's name.
	 *
	 * @since 1.6.0
	 */
	public function convert_menus() {
		$locations = get_nav_menu_locations();
		$copies    = (array) get_option( self::COPIES_OPTION, [] );
		$primary   = wp_get_nav_menu_object( $locations['primary'] ?? 0 );

		// The Mobile menu only stands in for the Primary one, so without a Primary menu to show, both keep core's fallback.
		if ( ! $primary || ! $this->has_content( $primary, $copies ) ) {
			update_option( self::OPTION, [], true );
			return;
		}

		$refs = [];

		foreach ( self::LOCATIONS as $location ) {
			$menu = wp_get_nav_menu_object( $locations[ $location ] ?? 0 );

			if ( ! $menu ) {
				continue;
			}

			$ref = (int) ( $copies[ $menu->term_id ] ?? 0 );

			if ( ! $this->is_published( $ref ) ) {
				$ref = $this->convert( $menu );
			}

			if ( $ref ) {
				$refs[ $location ]        = $ref;
				$copies[ $menu->term_id ] = $ref;
			}
		}

		update_option( self::COPIES_OPTION, $copies, false );
		update_option( self::OPTION, $refs, true );
	}

	/**
	 * The navigation menu for a menu location, or the Primary one when the location has none.
	 *
	 * @since 1.6.0
	 *
	 * @param string $location Menu location.
	 * @return int Navigation menu ID, or 0 to leave the block to core's fallback.
	 */
	public function get_ref( string $location ): int {
		$refs = (array) get_option( self::OPTION, [] );

		foreach ( array_unique( [ $location, 'primary' ] ) as $key ) {
			$ref = (int) ( $refs[ $key ] ?? 0 );

			if ( $this->is_published( $ref ) ) {
				return $ref;
			}
		}

		return 0;
	}

	/**
	 * Converts a classic menu into a new navigation menu named after it, as core's fallback names one.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Term $menu Classic menu.
	 * @return int Navigation menu ID, or 0 when the menu can't be converted.
	 */
	private function convert( WP_Term $menu ): int {
		$content = $this->get_blocks( $menu );

		if ( '' === $content ) {
			return 0;
		}

		// Slashed, since wp_insert_post() unslashes and the block attributes can hold escaped characters.
		$ref = wp_insert_post(
			wp_slash(
				[
					'post_type'    => 'wp_navigation',
					'post_status'  => 'publish',
					'post_title'   => $menu->name,
					'post_name'    => $menu->slug,
					'post_content' => $content,
				]
			),
			true
		);

		return is_wp_error( $ref ) ? 0 : $ref;
	}

	/**
	 * Whether a classic menu has a copy to reuse or items to convert.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Term $menu   Classic menu.
	 * @param array   $copies Navigation menu IDs the theme converted, by classic menu ID.
	 * @return bool
	 */
	private function has_content( WP_Term $menu, array $copies ): bool {
		return $this->is_published( (int) ( $copies[ $menu->term_id ] ?? 0 ) ) || '' !== $this->get_blocks( $menu );
	}

	/**
	 * A classic menu as Navigation block markup, the way core's fallback converts it, with numeric IDs.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Term $menu Classic menu.
	 * @return string Block markup, or an empty string when the menu has no items.
	 */
	private function get_blocks( WP_Term $menu ): string {
		$content = WP_Classic_To_Block_Menu_Converter::convert( $menu );

		if ( is_wp_error( $content ) || '' === $content ) {
			return '';
		}

		return serialize_blocks( $this->cast_ids( parse_blocks( (string) $content ) ) );
	}

	/**
	 * Stores each link's page or post ID as a number.
	 *
	 * The converter writes them as strings, which the Site Editor drops when it saves the
	 * menu, and core needs them to hide links to pages that aren't published.
	 *
	 * @since 1.6.0
	 *
	 * @param array $blocks Parsed menu blocks.
	 * @return array
	 */
	private function cast_ids( array $blocks ): array {
		foreach ( $blocks as $index => $block ) {
			if (
				in_array( $block['blockName'], [ 'core/navigation-link', 'core/navigation-submenu' ], true )
				&& isset( $block['attrs']['id'] )
				&& is_numeric( $block['attrs']['id'] )
			) {
				$blocks[ $index ]['attrs']['id'] = (int) $block['attrs']['id'];
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$blocks[ $index ]['innerBlocks'] = $this->cast_ids( $block['innerBlocks'] );
			}
		}

		return $blocks;
	}

	/**
	 * Whether an ID is a published navigation menu.
	 *
	 * @since 1.6.0
	 *
	 * @param int $ref Post ID.
	 * @return bool
	 */
	private function is_published( int $ref ): bool {
		return $ref && 'wp_navigation' === get_post_type( $ref ) && 'publish' === get_post_status( $ref );
	}
}
