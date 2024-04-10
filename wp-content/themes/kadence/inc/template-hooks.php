<?php
/**
 * Calls in content using theme hooks.
 *
 * @package kadence
 */

namespace Kadence;

use function Kadence\kadence;
use function add_action;

defined( 'ABSPATH' ) || exit;

/**
 * Kadence Header.
 *
 * @see Kadence\header_markup();
 */
add_action( 'kadence_header', 'Kadence\header_markup' );

/**
 * Kadence Header Rows
 *
 * @see Kadence\top_header();
 * @see Kadence\main_header();
 * @see Kadence\bottom_header();
 */
add_action( 'kadence_top_header', 'Kadence\top_header' );
add_action( 'kadence_main_header', 'Kadence\main_header' );
add_action( 'kadence_bottom_header', 'Kadence\bottom_header' );

/**
 * Kadence Header Columns
 *
 * @see Kadence\header_column();
 */
add_action( 'kadence_render_header_column', 'Kadence\header_column', 10, 2 );

/**
 * Kadence Mobile Header
 *
 * @see Kadence\mobile_header();
 */
add_action( 'kadence_mobile_header', 'Kadence\mobile_header' );

/**
 * Kadence Mobile Header Rows
 *
 * @see Kadence\mobile_top_header();
 * @see Kadence\mobile_main_header();
 * @see Kadence\mobile_bottom_header();
 */
add_action( 'kadence_mobile_top_header', 'Kadence\mobile_top_header' );
add_action( 'kadence_mobile_main_header', 'Kadence\mobile_main_header' );
add_action( 'kadence_mobile_bottom_header', 'Kadence\mobile_bottom_header' );

/**
 * Kadence Mobile Header Columns
 *
 * @see Kadence\mobile_header_column();
 */
add_action( 'kadence_render_mobile_header_column', 'Kadence\mobile_header_column', 10, 2 );

/**
 * Desktop Header Elements.
 *
 * @see Kadence\site_branding();
 * @see Kadence\primary_navigation();
 * @see Kadence\secondary_navigation();
 * @see Kadence\header_html();
 * @see Kadence\header_button();
 * @see Kadence\header_cart();
 * @see Kadence\header_social();
 * @see Kadence\header_search();
 */
add_action( 'kadence_site_branding', 'Kadence\site_branding' );
add_action( 'kadence_primary_navigation', 'Kadence\primary_navigation' );
add_action( 'kadence_secondary_navigation', 'Kadence\secondary_navigation' );
add_action( 'kadence_header_html', 'Kadence\header_html' );
add_action( 'kadence_header_button', 'Kadence\header_button' );
add_action( 'kadence_header_cart', 'Kadence\header_cart' );
add_action( 'kadence_header_social', 'Kadence\header_social' );
add_action( 'kadence_header_search', 'Kadence\header_search' );
/**
 * Mobile Header Elements.
 *
 * @see Kadence\mobile_site_branding();
 * @see Kadence\navigation_popup_toggle();
 * @see Kadence\mobile_navigation();
 * @see Kadence\mobile_html();
 * @see Kadence\mobile_button();
 * @see Kadence\mobile_cart();
 * @see Kadence\mobile_social();
 * @see Kadence\primary_navigation();
 */
add_action( 'kadence_mobile_site_branding', 'Kadence\mobile_site_branding' );
add_action( 'kadence_navigation_popup_toggle', 'Kadence\navigation_popup_toggle' );
add_action( 'kadence_mobile_navigation', 'Kadence\mobile_navigation' );
add_action( 'kadence_mobile_html', 'Kadence\mobile_html' );
add_action( 'kadence_mobile_button', 'Kadence\mobile_button' );
add_action( 'kadence_mobile_cart', 'Kadence\mobile_cart' );
add_action( 'kadence_mobile_social', 'Kadence\mobile_social' );

/**
 * Hero Title
 *
 * @see Kadence\hero_title();
 */
add_action( 'kadence_hero_header', 'Kadence\hero_title' );

/**
 * Page Title area
 *
 * @see Kadence\kadence_entry_header();
 */
add_action( 'kadence_entry_hero', 'Kadence\kadence_entry_header', 10, 2 );
add_action( 'kadence_entry_header', 'Kadence\kadence_entry_header', 10, 2 );

/**
 * Archive Title area
 *
 * @see Kadence\kadence_entry_archive_header();
 */
add_action( 'kadence_entry_archive_hero', 'Kadence\kadence_entry_archive_header', 10, 2 );
add_action( 'kadence_entry_archive_header', 'Kadence\kadence_entry_archive_header', 10, 2 );

/**
 * Singular Content
 *
 * @see Kadence\single_markup();
 */
add_action( 'kadence_single', 'Kadence\single_markup' );

/**
 * Singular Inner Content
 *
 * @see Kadence\single_content();
 */
add_action( 'kadence_single_content', 'Kadence\single_content' );

/**
 * 404 Content
 *
 * @see Kadence\get_404_content();
 */
add_action( 'kadence_404_content', 'Kadence\get_404_content' );

/**
 * Comments List
 *
 * @see Kadence\comments_list();
 */
add_action( 'kadence_comments', 'Kadence\comments_list' );

/**
 * Comment Form
 *
 * @see Kadence\comments_form();
 */
function check_comments_form_order() {
	$priority = ( kadence()->option( 'comment_form_before_list' ) ? 5 : 15 );
	add_action( 'kadence_comments', 'Kadence\comments_form', $priority );
}
add_action( 'kadence_comments', 'Kadence\check_comments_form_order', 1 );
/**
 * Archive Content
 *
 * @see Kadence\archive_markup();
 */
add_action( 'kadence_archive', 'Kadence\archive_markup' );

/**
 * Archive Entry Content.
 *
 * @see Kadence\loop_entry();
 */
add_action( 'kadence_loop_entry', 'Kadence\loop_entry' );

/**
 * Archive Entry thumbnail.
 *
 * @see Kadence\loop_entry_thumbnail();
 */
add_action( 'kadence_loop_entry_thumbnail', 'Kadence\loop_entry_thumbnail' );

/**
 * Archive Entry header.
 *
 * @see Kadence\loop_entry_header();
 */
add_action( 'kadence_loop_entry_content', 'Kadence\loop_entry_header', 10 );
/**
 * Archive Entry Summary.
 *
 * @see Kadence\loop_entry_summary();
 */
add_action( 'kadence_loop_entry_content', 'Kadence\loop_entry_summary', 20 );
/**
 * Archive Entry Footer.
 *
 * @see Kadence\loop_entry_footer();
 */
add_action( 'kadence_loop_entry_content', 'Kadence\loop_entry_footer', 30 );

/**
 * Archive Entry Taxonomies.
 *
 * @see Kadence\loop_entry_taxonomies();
 */
add_action( 'kadence_loop_entry_header', 'Kadence\loop_entry_taxonomies', 10 );
/**
 * Archive Entry Title.
 *
 * @see Kadence\loop_entry_title();
 */
add_action( 'kadence_loop_entry_header', 'Kadence\loop_entry_title', 20 );
/**
 * Archive Entry Meta.
 *
 * @see Kadence\loop_entry_meta();
 */
add_action( 'kadence_loop_entry_header', 'Kadence\loop_entry_meta', 30 );

/**
 * Main Call for Kadence footer
 *
 * @see Kadence\footer_markup();
 */
add_action( 'kadence_footer', 'Kadence\footer_markup' );

/**
 * Footer Top Row
 *
 * @see Kadence\top_footer();
 */
add_action( 'kadence_top_footer', 'Kadence\top_footer' );

/**
 * Footer Middle Row
 *
 * @see Kadence\middle_footer()
 */
add_action( 'kadence_middle_footer', 'Kadence\middle_footer' );

/**
 * Footer Bottom Row
 *
 * @see Kadence\bottom_footer()
 */
add_action( 'kadence_bottom_footer', 'Kadence\bottom_footer' );

/**
 * Footer Column
 *
 * @see Kadence\footer_column()
 */
add_action( 'kadence_render_footer_column', 'Kadence\footer_column', 10, 2 );


/**
 * Footer Elements
 *
 * @see Kadence\footer_html();
 * @see Kadence\footer_navigation()
 * @see Kadence\footer_social()
 */
add_action( 'kadence_footer_html', 'Kadence\footer_html' );
add_action( 'kadence_footer_navigation', 'Kadence\footer_navigation' );
add_action( 'kadence_footer_social', 'Kadence\footer_social' );

/**
 * WP Footer.
 *
 * @see Kadence\scroll_up();
 */
add_action( 'wp_footer', 'Kadence\scroll_up' );
