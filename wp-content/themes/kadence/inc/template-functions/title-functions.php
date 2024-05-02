<?php
/**
 * Calls in content using theme hooks.
 *
 * @package kadence
 */

namespace Kadence;

use function Kadence\kadence;
use function get_template_part;

defined( 'ABSPATH' ) || exit;
/**
 * Hero Title
 */
function hero_title() {
	if ( kadence()->show_hero_title() ) {
		if ( is_singular( get_post_type() ) ) {
			get_template_part( 'template-parts/content/entry_hero' );
		} else {
			get_template_part( 'template-parts/content/archive_hero' );
		}
	}
}
/**
 * Page Title area
 *
 * @param string $item_type the single post type.
 * @param string $area the title area.
 */
function kadence_entry_header( $item_type = 'post', $area = 'normal' ) {
	kadence()->render_title( $item_type, $area );
}

/**
 * Archive Title area
 *
 * @param string $item_type the single post type.
 * @param string $area the title area.
 */
function kadence_entry_archive_header( $item_type = 'post_archive', $area = 'normal' ) {
	kadence()->render_archive_title( $item_type, $area );
}
