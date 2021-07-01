<?php
/**
 * Data Structure Functionality
 *
 * Registers the posttype to represent the data
 * structure of the saved sidebars and contains
 * any CRUD logic.
 *
 * @package Easy_Custom_Sidebars
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace ECS\Data;

/**
 * Register Posttype
 *
 * Registers a new post type to hold any custom
 * sidebars created.
 *
 * @since 2.0.0
 */
function register_post_type_for_sidebars() {
	register_post_type(
		'sidebar_instance',
		[
			'labels'                => [
				'name'          => __( 'Sidebars', 'easy-custom-sidebars' ),
				'singular_name' => __( 'Sidebar', 'easy-custom-sidebars' ),
			],
			'public'                => false,
			'hierarchical'          => false,
			'rewrite'               => false,
			'delete_with_user'      => false,
			'query_var'             => false,
			'show_in_rest'          => true,
			'rest_base'             => 'easy-custom-sidebars',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => [
				'custom-fields',
				'title',
			],
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_post_type_for_sidebars' );

/**
 * Register Meta
 *
 * Register metadata to indicate all of the areas
 * (posts/taxonomies/templates/archives etc) in
 * the site that this sidebar is replacing.
 *
 * @since 2.0.0
 */
function register_metadata_for_sidebars() {
	register_meta(
		'post',
		'sidebar_replacement_id',
		[
			'object_subtype'    => 'sidebar_instance',
			'type'              => 'string',
			'description'       => __( 'The unique identifier of the existing sidebar that this custom sidebar will replace.', 'easy-custom-sidebars' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'sidebar_description',
		[
			'object_subtype'    => 'sidebar_instance',
			'type'              => 'string',
			'description'       => __( 'Description of the sidebar, displayed in the Widgets interface.', 'easy-custom-sidebars' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'sidebar_attachments',
		[
			'object_subtype' => 'sidebar_instance',
			'type'           => 'array',
			'single'         => true,
			'show_in_rest'   => [
				'schema' => [
					'items' => [
						'type'       => 'object',
						'properties' => [
							'id'              => [ 'type' => 'number' ],
							'data_type'       => [ 'type' => 'string' ],
							'attachment_type' => [ 'type' => 'string' ],
						],
					],
				],
			],
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_metadata_for_sidebars' );

/**
 * Get Sidebar Id.
 *
 * Gets the unique identifier by which
 * the custom sidebar will be registered
 * when register_sidebar() is invoked.
 *
 * @param int $post_id ID of a 'sidebar_instance' post.
 *
 * @return string Custom sidebar id.
 */
function get_sidebar_id( $post_id ) {
	return apply_filters( 'ecs_sidebar_id', "ecs-sidebar-{$post_id}", $post_id );
}

/**
 * Get Sidebar Description
 *
 * Gets the description text that will be
 * displayed in the Widgets interface.
 *
 * @param int $post_id ID of a 'sidebar_instance' post.
 */
function get_sidebar_description( $post_id ) {
	return apply_filters(
		'ecs_sidebar_description',
		get_post_meta( $post_id, 'sidebar_description', true ),
		$post_id
	);
}

/**
 * Get Sidebar Replacement ID
 *
 * Gets the id of the default registered sidebar
 * that this custom sidebar is replacing.
 *
 * @param int $post_id ID of a 'sidebar_instance' post.
 */
function get_sidebar_replacement_id( $post_id ) {
	return apply_filters(
		'ecs_sidebar_replacement_id',
		get_post_meta( $post_id, 'sidebar_replacement_id', true ),
		$post_id
	);
}

/**
 * Get Sidebar Attachments
 *
 * @param int     $post_id ID of a 'sidebar_instance' post.
 * @param boolean $with_metadata Adds additional metadata for each attachment.
 */
function get_sidebar_attachments( $post_id, $with_metadata = false ) {
	$attachments = get_post_meta( $post_id, 'sidebar_attachments', true );
	$attachments = empty( $attachments ) ? [] : $attachments;

	return apply_filters(
		'ecs_sidebar_attachments',
		$attachments,
		$post_id,
		$with_metadata
	);
}

/**
 * Add Attachment Metadata
 */
add_filter(
	'ecs_sidebar_attachments',
	function( $attachments, $post_id, $with_metadata ) {
		if ( ! $with_metadata ) {
			return $attachments;
		}

		return array_map(
			function ( $attachment ) {
				// Fallback for deleted items.
				$attachment['title'] = __( '(Not Found)', 'easy-custom-sidebars' );
				$attachment['label'] = __( 'Deleted', 'easy-custom-sidebars' );
				$attachment['link']  = site_url();

				// Post Type: Single.
				if ( 'post_type' === $attachment['attachment_type'] ) {
					$post_type = get_post_type_object( $attachment['data_type'] );

					if ( $post_type ) {
						$attachment['title'] = get_the_title( $attachment['id'] );
						$attachment['label'] = $post_type->labels->name;
						$attachment['link']  = get_page_link( $attachment['id'] );
					}
				}

				// Post Type: All.
				if ( 'post_type_all' === $attachment['attachment_type'] ) {
					$post_type = get_post_type_object( $attachment['data_type'] );

					if ( $post_type ) {
						/* translators: Sidebar attachment title (plural). */
						$attachment['title'] = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type->labels->name );
						$attachment['label'] = $post_type->labels->name;
						$attachment['link']  = get_admin_url( null, 'edit.php?post_type=' . $attachment['data_type'] );
					}
				}

				// Post Type Archive.
				if ( 'post_type_archive' === $attachment['attachment_type'] ) {
					$post_type = get_post_type_object( $attachment['data_type'] );

					if ( $post_type ) {
						/* translators: Sidebar attachment title (plural). */
						$attachment['title'] = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type->labels->name );
						/* translators: Sidebar attachment label (plural). */
						$attachment['label'] = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type->labels->name );
						$attachment['link']  = get_admin_url( null, 'edit.php?post_type=' . $attachment['data_type'] );
					}
				}

				// Taxonomy.
				if ( 'taxonomy' === $attachment['attachment_type'] ) {
					$term = get_term( $attachment['id'], $attachment['data_type'] );

					if ( $term && ! is_wp_error( $term ) ) {
						$attachment['title'] = $term->name;
						$attachment['label'] = get_taxonomy( $term->taxonomy )->labels->name;
						$attachment['link']  = get_term_link( $term->term_id );
					}
				}

				// Taxonomy All.
				if ( 'taxonomy_all' === $attachment['attachment_type'] ) {
					$taxonomy = get_taxonomy( $attachment['data_type'] );

					if ( $taxonomy ) {
						/* translators: Sidebar attachment title (plural). */
						$attachment['title'] = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $taxonomy->labels->name );

						/* translators: Sidebar attachment label (plural). */
						$attachment['label'] = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $taxonomy->labels->name );
						$attachment['link']  = get_admin_url( null, "edit-tags.php?taxonomy={$taxonomy->name}" );
					}
				}

				// All Posts in category.
				if ( 'category_posts' === $attachment['attachment_type'] ) {
					$term = get_term( $attachment['id'], $attachment['data_type'] );

					if ( $term && ! is_wp_error( $term ) ) {
						$attachment['title'] = $term->name;
						$attachment['label'] = __( 'All Posts In Category', 'easy-custom-sidebars' );
						$attachment['link']  = get_admin_url( null, "edit.php?taxonomy={$term->slug}" );
					}
				}

				// Author Archive.
				if ( 'author_archive' === $attachment['attachment_type'] ) {
					$user = get_userdata( $attachment['id'] );

					if ( $user ) {
						$attachment['title'] = $user->display_name;
						$attachment['label'] = __( 'Author Archive', 'easy-custom-sidebars' );
						$attachment['link']  = get_author_posts_url( $user->ID );
					}
				}

				// Template hierarchy.
				if ( 'template_hierarchy' === $attachment['attachment_type'] ) {
					$attachment['label'] = __( 'Template', 'easy-custom-sidebars' );
					$attachment['link']  = get_admin_url( null, 'edit.php?post_type=page' );

					// Template hierarchy: default.
					switch ( $attachment['data_type'] ) {
						case '404':
							$attachment['title'] = __( '404 (Page Not Found)', 'easy-custom-sidebars' );
							break;

						case 'author_archive_all':
							$attachment['title'] = __( 'Author Archive', 'easy-custom-sidebars' );
							break;

						case 'index_page':
							$attachment['title'] = __( 'Blog Index Page', 'easy-custom-sidebars' );
							break;

						case 'date_archive':
							$attachment['title'] = __( 'Date Archive', 'easy-custom-sidebars' );
							break;

						case 'search_results':
							$attachment['title'] = __( 'Search Results', 'easy-custom-sidebars' );
							break;
					}

					// Template hierarchy: page templates.
					$registered_page_templates   = wp_get_theme()->get_page_templates();
					$is_page_template_attachment = \strpos( $attachment['data_type'], 'page-template-' ) !== false;

					if ( $is_page_template_attachment ) {
						foreach ( $registered_page_templates as $name => $filename ) {
							if ( "page-template-{$name}" === $attachment['data_type'] ) {
								/* translators: Sidebar attachment title for page template attachments. */
								$attachment['title'] = \sprintf( __( 'Page Template: %s', 'easy-custom-sidebars' ), $filename );
							}
						}
					}
				}

				return $attachment;
			},
			$attachments
		);
	},
	10,
	3
);

/**
 * Get Default Registered Sidebars
 *
 * Gets every sidebar that wasn't
 * registered by this plugin.
 *
 * @return array sidebars.
 */
function get_default_registered_sidebars() {
	global $wp_registered_sidebars;

	return array_filter(
		$wp_registered_sidebars,
		function ( $sidebar ) {
			return empty( $sidebar['ecs_custom_sidebar'] );
		}
	);
}

/**
 * Get Custom Sidebar Replacements.
 *
 * Gets every custom sidebar replacement
 * ordered by name.
 *
 * @return array sidebars.
 */
function get_custom_sidebar_replacements() {
	global $wp_registered_sidebars;

	$sidebar_replacements = array_filter(
		$wp_registered_sidebars,
		function ( $sidebar ) {
			return ! empty( $sidebar['ecs_custom_sidebar'] );
		}
	);

	usort(
		$sidebar_replacements,
		function( $a, $b ) {
			return strnatcasecmp( $a['name'], $b['name'] );
		}
	);

	return $sidebar_replacements;
}

/**
 * Delete All Sidebars
 *
 * @return boolean true after all sidebars have been deleted.
 */
function delete_all_sidebars() {
	$all_sidebars = new \WP_Query(
		[
			'post_type'      => 'sidebar_instance',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		]
	);

	foreach ( $all_sidebars->posts as $sidebar_id ) {
		wp_delete_post( $sidebar_id, true );
	}

	if ( ! empty( $custom_sidebars->posts ) ) {
		wp_reset_postdata();
	}

	return true;
}

/**
 * Get All Page Templates
 *
 * @return array assoc array of registered templates.
 */
function get_page_templates() {
	return wp_get_theme()->get_page_templates();
}
