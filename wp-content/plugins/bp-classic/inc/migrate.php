<?php
/**
 * BP Classic Migration functions.
 *
 * @package bp-classic\inc
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Switch the Directory Pages post type.
 *
 * @todo This kind of function should probably be available in BuddyPress
 * & be separated from `bp_update_to_12_0()`.
 *
 * @since 1.0.0
 *
 * @param string $post_type The name of the post type to use for directory pages.
 */
function bp_classic_switch_directory_post_type( $post_type = '' ) {
	$needs_switch = is_multisite() && ! bp_is_root_blog();
	if ( $needs_switch ) {
		switch_to_blog( bp_get_root_blog_id() );
	}

	$directory_page_ids = bp_core_get_directory_page_ids( 'all' );
	$nav_menu_item_ids  = array();
	$old_post_type      = 'buddypress';

	if ( 'buddypress' === $post_type ) {
		$old_post_type = 'page';
	}

	// Query current directory pages.
	$directory_pages = get_posts(
		array(
			'numberposts' => count( $directory_page_ids ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_numberposts
			'post_type'   => $old_post_type,
			'include'     => $directory_page_ids,
		)
	);

	if ( ! $directory_pages ) {
		return;
	}

	// Do not check post slugs nor post types.
	remove_filter( 'wp_unique_post_slug', 'bp_core_set_unique_directory_page_slug', 10 );

	// Update Directory pages post types.
	foreach ( $directory_pages as $directory_page ) {
		$nav_menu_item_ids[] = $directory_page->ID;

		// Switch the post type.
		wp_update_post(
			array(
				'ID'          => $directory_page->ID,
				'post_type'   => $post_type,
				'post_status' => 'publish',
			)
		);
	}

	// Restore the filter.
	add_filter( 'wp_unique_post_slug', 'bp_core_set_unique_directory_page_slug', 10 );

	// Update nav menu items!
	$nav_menus = wp_get_nav_menus( array( 'hide_empty' => true ) );
	foreach ( $nav_menus as $nav_menu ) {
		$items = wp_get_nav_menu_items( $nav_menu->term_id );
		foreach ( $items as $item ) {
			$item_object_id = (int) $item->object_id;

			if ( $old_post_type !== $item->object || ! in_array( $item_object_id, $nav_menu_item_ids, true ) ) {
				continue;
			}

			wp_update_nav_menu_item(
				$nav_menu->term_id,
				$item->ID,
				array(
					'menu-item-db-id'       => $item->db_id,
					'menu-item-object-id'   => $item_object_id,
					'menu-item-object'      => $post_type,
					'menu-item-parent-id'   => $item->menu_item_parent,
					'menu-item-position'    => $item->menu_order,
					'menu-item-type'        => 'post_type',
					'menu-item-title'       => $item->title,
					'menu-item-url'         => $item->url,
					'menu-item-description' => $item->description,
					'menu-item-attr-title'  => $item->attr_title,
					'menu-item-target'      => $item->target,
					'menu-item-classes'     => implode( ' ', (array) $item->classes ),
					'menu-item-xfn'         => $item->xfn,
					'menu-item-status'      => 'publish',
				)
			);
		}
	}

	if ( $needs_switch ) {
		restore_current_blog();
	}
}

/**
 * Make sure to activate a default WordPress theme if BP Default is active.
 *
 * @since 1.0.0
 */
function bp_classic_restore_default_theme() {
	// If the current active template theme is not BP Default from the this plugin, stop.
	if ( false === strpos( get_template_directory(), 'bp-classic/themes/bp-default' ) ) {
		return;
	}

	// Force refresh theme roots.
	delete_site_transient( 'theme_roots' );

	// Switch to WordPress's default theme if current parent or child theme
	// depend on bp-default. This is to prevent white screens of doom.
	if ( in_array( 'bp-default', array( get_template(), get_stylesheet() ), true ) ) {
		switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
		update_option( 'template_root', get_raw_theme_root( WP_DEFAULT_THEME, true ) );
		update_option( 'stylesheet_root', get_raw_theme_root( WP_DEFAULT_THEME, true ) );
	}
}
