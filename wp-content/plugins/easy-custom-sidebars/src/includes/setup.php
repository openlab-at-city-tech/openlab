<?php
/**
 * Plugin Setup
 *
 * Contains information about the file structure of
 * the plugin along with any setup logic.
 *
 * @package Easy_Custom_Sidebars
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace ECS\Setup;

use ECS\Data;

/**
 * Register All Custom Sidebars
 *
 * Registers all custom sidebars as widget areas
 * within WordPress. All custom sidebars that are
 * replacing default sidebars will inherit the
 * properties of the default sidebar.
 *
 * @since 2.0.0
 */
function register_all_custom_sidebars() {
	$custom_sidebars = new \WP_Query(
		[
			'post_type'      => 'sidebar_instance',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'fields'         => 'ids',
		]
	);

	foreach ( $custom_sidebars->posts as $sidebar_id ) {
		register_sidebar( get_sidebar_args( $sidebar_id ) );
	}

	if ( ! empty( $custom_sidebars->posts ) ) {
		wp_reset_postdata();
	}
}
add_action( 'widgets_init', __NAMESPACE__ . '\\register_all_custom_sidebars' );

/**
 * Register Custom Sidebar
 *
 * All custom sidebars will inherit the properties
 * of the original sidebar it is replacing. This
 * function must be used inside the loop.
 *
 * TODO: Handle permanent sidebars created by the plugin.
 *
 * @uses global $wp_registered_sidebars
 * @param int $post_id ID of the current 'sidebar-instance'
 *                     post in the loop.
 * @since 2.0.0
 */
function get_sidebar_args( $post_id ) {
	global $wp_registered_sidebars;

	$original_sidebar_id = get_post_meta( $post_id, 'sidebar_replacement_id', true );
	$description         = get_post_meta( $post_id, 'sidebar_description', true );

	$args = [
		'ecs_custom_sidebar' => true,
		'name'               => get_the_title( $post_id ),
		'id'                 => Data\get_sidebar_id( $post_id ),
		'description'        => Data\get_sidebar_description( $post_id ),
	];

	if ( isset( $wp_registered_sidebars[ $original_sidebar_id ] ) ) {
		$original_sidebar = $wp_registered_sidebars[ $original_sidebar_id ];

		$args = wp_parse_args(
			[
				'class'         => $original_sidebar['class'],
				'before_widget' => $original_sidebar['before_widget'],
				'after_widget'  => $original_sidebar['after_widget'],
				'before_title'  => $original_sidebar['before_title'],
				'after_title'   => $original_sidebar['after_title'],
			],
			$args
		);
	} else {
		$args = wp_parse_args(
			[
				'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
				'after_widget'  => "</div></div>",
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			],
			$args
		);
	}

	return $args;
}

/**
 * Get Plugin File URL
 *
 * Gets the URL to the /src/ directory
 * in the plugin directory with the
 * trailing slash.
 *
 * @return string URL to the src directory with the trailing slash.
 * @since 2.0.0
 */
function get_plugin_src_url() {
	return trailingslashit( plugins_url( 'easy-custom-sidebars/src' ) );
}

/**
 * Get Plugin File Path
 *
 * Gets the file path to the /src/ directory
 * in the plugin directory.
 *
 * @return string Filepath to the src directory.
 * @since 2.0.0
 */
function get_plugin_src_file_path() {
	return plugin_dir_path( __DIR__ );
}
