<?php

/**
 * To port from cbox-openlab-core:
 *
 * 1. Copy the following files:
 *   a. includes/site-template.php -> lib/site-template.php
 *   b. build/site-templates.css -> css/site-templates.css
 *   c. build/site-templates.js -> js/site-templates.js
 *   d. build/site-templates-admin.js -> js/site-templates-admin.js
 *   e. build/site-templates-admin.css -> css/site-templates-admin.css
 *   f. build/site-templates-default-category.js -> js/site-templates-default-category.js
 *   g. views/site-template/ -> parts/site-template/
 *   h. classes/API/Sites.php -> wp-content/plugins/wds-citytech/includes/cbox-polyfills/class-cbox-sites-endpoint.php
 *
 * 2. Make the following changes:
 *   a. Change paths in cboxol_register_site_template_assets()
 *   b. In cboxol_load_site_template_view(), change reference to 'views' directory to [theme]/parts/
 */

/**
 * Registers template picker assets.
 *
 * @return void
 */
function cboxol_register_site_template_assets() {
	wp_register_style(
		'cboxol-site-template-picker-style',
		get_stylesheet_directory_uri() . '/css/site-templates.css',
		[],
		CBOXOL_ASSET_VER
	);

	wp_register_script(
		'cboxol-site-template-picker-script',
		get_stylesheet_directory_uri() . '/js/site-templates.js',
		[],
		CBOXOL_ASSET_VER,
		true
	);

	wp_register_script(
		'cboxol-site-template-picker-admin-script',
		get_stylesheet_directory_uri() . '/js/site-templates-admin.js',
		[ 'jquery', 'select-js' ],
		CBOXOL_ASSET_VER,
		true
	);

	wp_register_style(
		'cboxol-site-template-picker-admin-style',
		get_stylesheet_directory_uri() . '/css/site-templates-admin.css',
		[],
		CBOXOL_ASSET_VER
	);

	wp_register_script(
		'cboxol-site-templates-default-category',
		get_stylesheet_directory_uri() . '/js/site-templates-default-category.js',
		[],
		CBOXOL_ASSET_VER,
		true
	);

	$all_categories = get_terms(
		[
			'taxonomy'   => 'cboxol_template_category',
			'hide_empty' => false,
		]
	);

	$category_map = [];
	foreach ( $all_categories as $cat ) {
		$category_map[ $cat->term_id ] = cboxol_get_term_group_types( $cat->term_id );
	}

	$default_template_map = [];
	foreach ( cboxol_get_group_types() as $group_type ) {
		$default_template_map[ $group_type->get_slug() ] = $group_type->get_site_template_id();
	}

	$member_types_allowed_to_create_courses = array_filter(
		cboxol_get_member_types(),
		function( $member_type ) {
			return $member_type->get_can_create_courses();
		}
	);

	$locale = get_locale();
	$lang   = substr( $locale, 0, 2 );

	wp_localize_script(
		'cboxol-site-template-picker-script',
		'SiteTemplatePicker',
		[
			'endpoint'    => rest_url( 'wp/v2/site-templates' ),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
			'perPage'     => 6,
			'categoryMap' => $category_map,
			'defaultMap'  => $default_template_map,
			'messages'    => [
				'loading'   => esc_html__( 'Loading Templates...', 'commons-in-a-box' ),
				'noResults' => esc_html__( 'No templates were found.', 'commons-in-a-box' ),
			],
		]
	);

	wp_localize_script(
		'cboxol-site-template-picker-admin-script',
		'SiteTemplatePickerAdmin',
		[
			'endpoint'                => rest_url( 'cboxol/v1/sites' ),
			'nonce'                   => wp_create_nonce( 'wp_rest' ),
			'lang'                    => $lang,
			'categoryMap'             => $category_map,
			'courseGroupTypeSlug'     => cboxol_get_course_group_type()->get_slug(),
			'courseCreateMemberTypes' => array_keys( $member_types_allowed_to_create_courses ),
		]
	);

	wp_localize_script(
		'cboxol-site-templates-default-category',
		'SiteTemplatesDefaultCategory',
		[
			'defaultCategoryId' => cboxol_get_default_site_template_category_id(),
		]
	);
}
add_action( 'init', 'cboxol_register_site_template_assets', 20 );

/**
 * Registers the "Site Template" post type.
 *
 * @return void
 */
function cboxol_register_site_template_post_type() {
	if ( ! bp_is_root_blog() ) {
		return;
	}

	register_post_type(
		'cboxol_site_template',
		[
			'labels'               => [
				'name'                  => __( 'Site Templates', 'commons-in-a-box' ),
				'singular_name'         => __( 'Site Template', 'commons-in-a-box' ),
				'all_items'             => __( 'All Site Templates', 'commons-in-a-box' ),
				'archives'              => __( 'Site Template Archives', 'commons-in-a-box' ),
				'attributes'            => __( 'Site Template Attributes', 'commons-in-a-box' ),
				'insert_into_item'      => __( 'Insert into Site Template', 'commons-in-a-box' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Site Template', 'commons-in-a-box' ),
				'featured_image'        => _x( 'Featured Image', 'cboxol_site_template', 'commons-in-a-box' ),
				'set_featured_image'    => _x( 'Set featured image', 'cboxol_site_template', 'commons-in-a-box' ),
				'remove_featured_image' => _x( 'Remove featured image', 'cboxol_site_template', 'commons-in-a-box' ),
				'use_featured_image'    => _x( 'Use as featured image', 'cboxol_site_template', 'commons-in-a-box' ),
				'filter_items_list'     => __( 'Filter Site Templates list', 'commons-in-a-box' ),
				'items_list_navigation' => __( 'Site Templates list navigation', 'commons-in-a-box' ),
				'items_list'            => __( 'Site Templates list', 'commons-in-a-box' ),
				'new_item'              => __( 'New Site Template', 'commons-in-a-box' ),
				'add_new'               => __( 'Add New', 'commons-in-a-box' ),
				'add_new_item'          => __( 'Add New Site Template', 'commons-in-a-box' ),
				'edit_item'             => __( 'Edit Site Template', 'commons-in-a-box' ),
				'view_item'             => __( 'View Site Template', 'commons-in-a-box' ),
				'view_items'            => __( 'View Site Templates', 'commons-in-a-box' ),
				'search_items'          => __( 'Search Site Templates', 'commons-in-a-box' ),
				'not_found'             => __( 'No Site Templates found', 'commons-in-a-box' ),
				'not_found_in_trash'    => __( 'No Site Templates found in trash', 'commons-in-a-box' ),
				'parent_item_colon'     => __( 'Parent Site Template:', 'commons-in-a-box' ),
				'menu_name'             => __( 'Site Templates', 'commons-in-a-box' ),
			],
			'public'               => false,
			'hierarchical'         => false,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'show_in_nav_menus'    => false,
			'register_meta_box_cb' => 'cboxol_register_site_template_meta_boxes',
			'supports'             => [ 'title', 'excerpt', 'thumbnail' ],
			'has_archive'          => false,
			'rewrite'              => false,
			'query_var'            => false,
			'delete_with_user'     => false,
			'menu_position'        => null,
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			'menu_icon'            => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" height="48" width="48"><path fill="black" d="M15 37.95Q13.8 37.95 12.9 37.05Q12 36.15 12 34.95V6.95Q12 5.75 12.9 4.85Q13.8 3.95 15 3.95H37Q38.2 3.95 39.1 4.85Q40 5.75 40 6.95V34.95Q40 36.15 39.1 37.05Q38.2 37.95 37 37.95ZM9 43.95Q7.8 43.95 6.9 43.05Q6 42.15 6 40.95V10.8H9V40.95Q9 40.95 9 40.95Q9 40.95 9 40.95H32.7V43.95Z"/></svg>' ),
			'show_in_rest'         => true,
			'rest_base'            => 'site-templates',
			'capabilities'         => [
				'delete_posts'           => 'delete_sites',
				'delete_post'            => 'delete_sites',
				'delete_published_posts' => 'delete_sites',
				'delete_private_posts'   => 'delete_sites',
				'delete_others_posts'    => 'delete_sites',
				'edit_post'              => 'manage_sites',
				'edit_posts'             => 'manage_sites',
				'edit_others_posts'      => 'manage_sites',
				'edit_published_posts'   => 'manage_sites',
				'read_post'              => 'read',
				'read_private_posts'     => 'read',
				'publish_posts'          => 'create_sites',
			],
		]
	);
}
add_action( 'init', 'cboxol_register_site_template_post_type' );

/**
 * Registers the "Site Template Category" taxonomy.
 *
 * @return void
 */
function cboxol_register_site_template_category_taxonomy() {
	register_taxonomy(
		'cboxol_template_category',
		[ 'cboxol_site_template' ],
		[
			'labels'            => [
				'name'                       => __( 'Template Categories', 'commons-in-a-box' ),
				'singular_name'              => _x( 'Template Category', 'taxonomy general name', 'commons-in-a-box' ),
				'search_items'               => __( 'Search Template Categories', 'commons-in-a-box' ),
				'popular_items'              => __( 'Popular Template Categories', 'commons-in-a-box' ),
				'all_items'                  => __( 'All Template Categories', 'commons-in-a-box' ),
				'parent_item'                => __( 'Parent Template Category', 'commons-in-a-box' ),
				'parent_item_colon'          => __( 'Parent Template Category:', 'commons-in-a-box' ),
				'edit_item'                  => __( 'Edit Template Category', 'commons-in-a-box' ),
				'update_item'                => __( 'Update Template Category', 'commons-in-a-box' ),
				'view_item'                  => __( 'View Template Category', 'commons-in-a-box' ),
				'add_new_item'               => __( 'Add New Template Category', 'commons-in-a-box' ),
				'new_item_name'              => __( 'New Template Category', 'commons-in-a-box' ),
				'separate_items_with_commas' => __( 'Separate template Categories with commas', 'commons-in-a-box' ),
				'add_or_remove_items'        => __( 'Add or remove template categories', 'commons-in-a-box' ),
				'choose_from_most_used'      => __( 'Choose from the most used template categories', 'commons-in-a-box' ),
				'not_found'                  => __( 'No template categories found.', 'commons-in-a-box' ),
				'no_terms'                   => __( 'No template categories', 'commons-in-a-box' ),
				'menu_name'                  => __( 'Template categories', 'commons-in-a-box' ),
				'items_list_navigation'      => __( 'Template categories list navigation', 'commons-in-a-box' ),
				'items_list'                 => __( 'Template categories list', 'commons-in-a-box' ),
				'most_used'                  => _x( 'Most Used', 'template_category', 'commons-in-a-box' ),
				'back_to_items'              => __( '&larr; Back to Template categories', 'commons-in-a-box' ),
			],
			'hierarchical'      => true,
			'public'            => false,
			'show_in_nav_menus' => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'rewrite'           => false,
			'show_in_rest'      => true,
			'rest_base'         => 'template_category',
			'capabilities'      => [
				'manage_terms' => 'manage_sites',
				'edit_terms'   => 'manage_sites',
				'delete_terms' => 'manage_sites',
				'assign_terms' => 'manage_sites',
			],
			'meta_box_cb'       => 'cboxol_site_template_category_meta_box_cb',
		]
	);
}
add_action( 'init', 'cboxol_register_site_template_category_taxonomy', 15 );

/**
 * Load the 'default category' script on post-new.php.
 */
function cboxol_load_default_site_template_category_script( $hook ) {
	if ( 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'cboxol_site_template' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script( 'cboxol-site-templates-default-category' );
}
add_action( 'admin_enqueue_scripts', 'cboxol_load_default_site_template_category_script' );

/**
 * Handle post type specific metaboxes.
 *
 * @return void
 */
function cboxol_register_site_template_meta_boxes() {
	// Manually remove metabox instead of 'supports' args to make it available in REST API.
	remove_meta_box( 'postexcerpt', 'cboxol_site_template', 'normal' );

	add_meta_box(
		'template-description',
		__( 'Description', 'commons-in-a-box' ),
		'cboxol_render_site_template_description',
		'cboxol_site_template',
		'normal',
		'core'
	);

	add_meta_box(
		'template-site',
		__( 'Template Site', 'commons-in-a-box' ),
		'cboxol_render_template_site',
		'cboxol_site_template',
		'normal',
		'core'
	);

	add_meta_box(
		'template-visibility',
		__( 'Template Visibility', 'commons-in-a-box' ),
		'cboxol_render_template_visibility',
		'cboxol_site_template',
		'normal',
		'core'
	);
}

/**
 * Visually replace "Excerpt" metabox with "Description" metabox
 *
 * @param \WP_Post $post
 * @return void
 */
function cboxol_render_site_template_description( $post ) {
	cboxol_load_site_template_view( 'admin/description.php', [ 'description' => $post->post_excerpt ] );
}

/**
 * Render callback for the Template Site metabox.
 *
 * @param \WP_Post $post
 * @return void
 */
function cboxol_render_template_site( $post ) {
	$site_id = cboxol_get_template_site_id( $post->ID );

	$site_name = '';
	$site_url  = '';
	if ( $site_id ) {
		$site = get_site( $site_id );
		if ( $site ) {
			$site_name = $site->blogname;
			$site_url  = $site->siteurl;
		}
	}

	wp_enqueue_script( 'cboxol-site-template-picker-admin-script' );

	cboxol_load_site_template_view(
		'admin/template-site.php',
		[
			'is_create' => 'auto-draft' === $post->post_status,
			'site_id'   => $site_id,
			'site_name' => $site_name,
			'site_url'  => $site_url,
		]
	);
}

/**
 * Render callback for the Template Visibility metabox.
 *
 * @param \WP_Post $post
 * @return void
 */
function cboxol_render_template_visibility( $post ) {
	$limit_by_member_types = cboxol_is_template_visibility_limited_by_member_type( $post->ID );
	$selected_member_types = cboxol_get_template_member_types( $post->ID );

	$limit_by_academic_units = cboxol_is_template_visibility_limited_by_academic_unit( $post->ID );
	$selected_academic_units = cboxol_get_template_academic_units( $post->ID );

	cboxol_load_site_template_view(
		'admin/visibility.php',
		[
			'limit_by_member_types'   => $limit_by_member_types,
			'selected_member_types'   => $selected_member_types,
			'limit_by_academic_units' => $limit_by_academic_units,
			'selected_academic_units' => $selected_academic_units,
		]
	);
}

/**
 * Determines whether a template's visibility is limited to specific member types.
 *
 * @since 1.6.0
 *
 * @param int $template_id The site template ID.
 * @return bool
 */
function cboxol_is_template_visibility_limited_by_member_type( $template_id ) {
	return (bool) get_post_meta( $template_id, 'cboxol_limit_template_by_member_type', true );
}

/**
 * Determines the member types that a template is limited to.
 *
 * @since 1.6.0
 *
 * @param int $template_id The site template ID.
 * @return \CBOX\OL\MemberType[]
 */
function cboxol_get_template_member_types( $template_id ) {
	$selected_member_types = (array) get_post_meta( $template_id, 'cboxol_template_member_type' );

	$types = [];
	foreach ( $selected_member_types as $slug ) {
		$type = cboxol_get_member_type( $slug );
		if ( $type ) {
			$types[ $slug ] = $type;
		}
	}

	return $types;
}

/**
 * Determines the academic units that a template is limited to.
 *
 * @since 1.6.0
 *
 * @param int $template_id The site template ID.
 * @return \CBOX\OL\AcademicUnit[]
 */
function cboxol_get_template_academic_units( $template_id ) {
	$selected_academic_units = (array) get_post_meta( $template_id, 'cboxol_template_academic_unit' );

	$units = [];
	foreach ( $selected_academic_units as $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'cboxol_acadunit' !== $post->post_type ) {
			continue;
		}

		$unit = cboxol_get_academic_unit( $post->post_name );
		if ( $unit ) {
			$units[ $post_id ] = $unit;
		}
	}

	return apply_filters( 'cboxol_get_template_academic_units', $units, $template_id );
}

/**
 * Determines whether a template's visibility is limited to specific academic units.
 *
 * @since 1.6.0
 *
 * @param int $template_id The site template ID.
 * @return bool
 */
function cboxol_is_template_visibility_limited_by_academic_unit( $template_id ) {
	return (bool) get_post_meta( $template_id, 'cboxol_limit_template_by_academic_unit', true );
}

/**
 * Saves visibility data on template save.
 *
 * @since 1.6.0
 *
 * @param int      $post_id The site template ID.
 * @param \WP_Post $post    The site template object.
 * @return void
 */
function cboxol_save_template_visibility( $post_id, \WP_Post $post ) {
	if ( ! current_user_can( 'manage_network_options' ) ) {
		return;
	}

	if ( empty( $_POST['cboxol-template-visibility-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'cboxol-template-visibility', 'cboxol-template-visibility-nonce' );

	// Member types.
	$limit_by_member_types = isset( $_POST['template-visibility-limit-by-member-type'] ) && 'yes' === $_POST['template-visibility-limit-by-member-type'];

	if ( $limit_by_member_types ) {
		update_post_meta( $post->ID, 'cboxol_limit_template_by_member_type', 1 );
	} else {
		delete_post_meta( $post->ID, 'cboxol_limit_template_by_member_type' );
	}

	$selected_member_types = isset( $_POST['template-visibility-limit-to-member-types'] ) ? array_map( 'sanitize_text_field', $_POST['template-visibility-limit-to-member-types'] ) : [];
	$selected_member_types = array_filter(
		$selected_member_types,
		function( $slug ) {
			return cboxol_get_member_type( $slug );
		}
	);

	delete_post_meta( $post->ID, 'cboxol_template_member_type' );

	foreach ( $selected_member_types as $slug ) {
		add_post_meta( $post->ID, 'cboxol_template_member_type', $slug );
	}

	// Academic units.
	$limit_by_academic_units = isset( $_POST['template-visibility-limit-by-academic-unit'] ) && 'yes' === $_POST['template-visibility-limit-by-academic-unit'];

	if ( $limit_by_academic_units ) {
		update_post_meta( $post->ID, 'cboxol_limit_template_by_academic_unit', 1 );
	} else {
		delete_post_meta( $post->ID, 'cboxol_limit_template_by_academic_unit' );
	}

	$selected_academic_units = isset( $_POST['template-visibility-limit-to-academic-unit'] ) ? array_map( 'sanitize_text_field', $_POST['template-visibility-limit-to-academic-unit'] ) : [];
	$selected_academic_units = array_filter(
		$selected_academic_units,
		function( $post_id ) {
			$post = get_post( $post_id );
			return $post && 'cboxol_acadunit' === $post->post_type;
		}
	);

	delete_post_meta( $post->ID, 'cboxol_template_academic_unit' );

	foreach ( $selected_academic_units as $selected_academic_unit_id ) {
		add_post_meta( $post->ID, 'cboxol_template_academic_unit', $selected_academic_unit_id );
	}
}
add_action( 'save_post', 'cboxol_save_template_visibility', 15, 2 );

/**
 * Render a view.
 *
 * @param string $name Name of the view.
 * @param array  $data Data passed to the view.
 * @return void
 *
 * @throws \Exception
 */
function cboxol_load_site_template_view( $name = '', array $data = [] ) {
	$path = get_stylesheet_directory() . '/parts/site-template/' . $name;

	if ( ! file_exists( $path ) ) {
		throw new \Exception( sprintf( 'Unable to locate view file: %s', $path ) );
	}

	extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	require $path;
}

/**
 * Create associated template site.
 *
 * @param int      $post_id The site template ID.
 * @param \WP_Post $post    The site template object.
 * @return void
 */
function cboxol_create_site_template( $post_id, \WP_Post $post ) {
	if ( isset( $post->post_status ) && 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( 'cboxol_site_template' !== $post->post_type ) {
		return;
	}

	$site_id = cboxol_get_template_site_id( $post_id );
	if ( $site_id ) {
		return;
	}

	// Use timestamp as a hash to ensure uniqueness.
	$slug = sprintf( 'site-template-%s-%s', $post->post_name, time() );

	// translators: Template name.
	$name = sprintf( __( 'Site Template - %s', 'commons-in-a-box' ), esc_html( $post->post_title ) );

	$site_id = cboxol_create_site_for_template( $post_id, $slug, $name );
}
add_action( 'save_post', 'cboxol_create_site_template', 10, 2 );

/**
 * Saves the template site ID on template save.
 */
function cboxol_save_template_site_id( $post_id, \WP_Post $post ) {
	if ( ! current_user_can( 'manage_network_options' ) ) {
		return;
	}

	if ( empty( $_POST['cboxol-template-site-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'cboxol-template-site', 'cboxol-template-site-nonce' );

	$template_site_id = isset( $_POST['template-site-id'] ) ? intval( $_POST['template-site-id'] ) : null;

	// Cannot be unset. This is important to avoid race conditions during the create process.
	if ( ! $template_site_id ) {
		return;
	}

	update_post_meta( $post->ID, '_template_site_id', $template_site_id );
}
add_action( 'save_post', 'cboxol_save_template_site_id', 15, 2 );

/**
 * Creates a template site.
 *
 * @param int    $template_id
 * @param string $slug
 * @param string $name
 * @return int $site_id
 */
function cboxol_create_site_for_template( $template_id, $slug, $name ) {
	$current_network = get_network();

	if ( is_subdomain_install() ) {
		$site_domain = preg_replace( '|^www\.|', '', $current_network->domain );
		$domain      = $slug . '.' . $site_domain;
		$path        = '/';
	} else {
		$domain = $current_network->domain;
		$path   = $current_network->path . $slug . '/';
	}

	$site_id = wp_insert_site(
		[
			'domain'  => $domain,
			'path'    => $path,
			'user_id' => get_current_user_id(),
			/* translators: Site template name */
			'title'   => $name,
		]
	);

	if ( is_wp_error( $site_id ) ) {
		return;
	}

	switch_to_blog( $site_id );

	// Create default menu items.
	$menu_name = wp_slash( __( 'Main Menu', 'commons-in-a-box' ) );
	$menu_id   = wp_create_nav_menu( $menu_name );

	$group_menu_item_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'   => __( 'Group Home', 'commons-in-a-box' ),
			'menu-item-url'     => home_url( '/group-profile' ),
			'menu-item-status'  => 'publish',
			'menu-item-type'    => 'custom',
			'menu-item-classes' => 'group-profile-link',
		)
	);

	$home_menu_item_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'   => __( 'Home', 'commons-in-a-box' ),
			'menu-item-url'     => home_url( '/' ),
			'menu-item-status'  => 'publish',
			'menu-item-type'    => 'custom',
			'menu-item-classes' => 'home',
		)
	);

	// Store flag for injected custom menu items
	add_term_meta(
		$menu_id,
		'cboxol_custom_menus',
		array(
			'group' => is_wp_error( $group_menu_item_id ) ? 0 : $group_menu_item_id,
			'home'  => is_wp_error( $home_menu_item_id ) ? 0 : $home_menu_item_id,
		),
		true
	);

	$locations            = get_theme_mod( 'nav_menu_locations' );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	restore_current_blog();

	// Save template ID for syncing.
	update_site_meta( $site_id, '_site_template_id', $template_id );

	// Save template site ID for syncing.
	update_post_meta( $template_id, '_template_site_id', $site_id );

	return $site_id;
}

/**
 * Set template site status as "Deleted".
 * This doesn't actually delete the site from the DB.
 *
 * @param int $post_id The site template ID.
 * @return void
 */
function cboxol_delete_site_template( $post_id ) {
	// WP < 5.5 compatibility.
	$post = get_post( $post_id );

	if ( 'cboxol_site_template' !== $post->post_type ) {
		return;
	}

	$site_id = cboxol_get_template_site_id( $post_id );

	// Bail if template has no associated site.
	if ( ! $site_id ) {
		return;
	}

	update_blog_status( $site_id, 'deleted', 1 );
}
add_action( 'before_delete_post', 'cboxol_delete_site_template' );

/**
 * Sets the post updated messages for the "Site Template Category" taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the "Site Template Category" taxonomy.
 */
function cboxol_site_template_category_updated_messages( $messages ) {
	$messages['cboxol_template_category'] = [
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Template category added.', 'cboxol-site-template-picker' ),
		2 => __( 'Template category deleted.', 'cboxol-site-template-picker' ),
		3 => __( 'Template category updated.', 'cboxol-site-template-picker' ),
		4 => __( 'Template category not added.', 'cboxol-site-template-picker' ),
		5 => __( 'Template category not updated.', 'cboxol-site-template-picker' ),
		6 => __( 'Template categories deleted.', 'cboxol-site-template-picker' ),
	];

	return $messages;
}
add_filter( 'term_updated_messages', 'cboxol_site_template_category_updated_messages' );

/**
 * Gets the site ID associated with a template.
 *
 * @param int $template_id
 * @return int
 */
function cboxol_get_template_site_id( $template_id ) {
	return (int) get_post_meta( $template_id, '_template_site_id', true );
}

/**
 * Gets the group types associated with a term.
 *
 * @param int $term_id
 * @return array
 */
function cboxol_get_term_group_types( $term_id ) {
	return (array) get_term_meta( $term_id, 'cboxol_group_type', false );
}

/**
 * Gets the default site template category ID.
 *
 * @since 1.4.0
 *
 * @return int
 */
function cboxol_get_default_site_template_category_id() {
	return (int) get_option( 'cboxol_default_site_template_category' );
}

/**
 * Meta box callback for site template category taxonomy.
 *
 * A wrapper for post_categories_meta_box() (the default) that adds our label filters.
 *
 * @return void
 */
function cboxol_site_template_category_meta_box_cb( $post, $box ) {
	add_filter( 'get_terms', 'cboxol_append_group_types_to_site_template_category_labels' );
	post_categories_meta_box( $post, $box );
	remove_filter( 'get_terms', 'cboxol_append_group_types_to_site_template_category_labels' );
}

/**
 * Appends associated group types to site template category labels.
 *
 * @param array $terms
 * @return array
 */
function cboxol_append_group_types_to_site_template_category_labels( $terms ) {
	$all_group_types = cboxol_get_group_types();

	foreach ( $terms as &$term ) {
		if ( ! ( $term instanceof \WP_Term ) ) {
			continue;
		}

		$type_labels = cboxol_get_term_group_type_labels( $term->term_id );

		if ( $type_labels ) {
			$term->name .= ' (' . implode( ', ', $type_labels ) . ')';
		} else {
			$term->name .= ' ' . __( '(no group types)', 'commons-in-a-box' );
		}
	}

	return $terms;
}

/**
 * Gets an array of the labels belonging to a term's group types.
 *
 * @param int $term_id
 * @return array
 */
function cboxol_get_term_group_type_labels( $term_id ) {
	$all_group_types = cboxol_get_group_types();

	$type_labels = array_map(
		function( $type_slug ) use ( $all_group_types ) {
			if ( isset( $all_group_types[ $type_slug ] ) ) {
				return $all_group_types[ $type_slug ]->get_label( 'plural' );
			}
		},
		cboxol_get_term_group_types( $term_id )
	);

	return array_filter( $type_labels );
}

/**
 * Adds the group-type selecton interface on edit-tags.php.
 *
 * @return void
 */
function cboxol_site_template_add_group_type_fields_to_edit_tags() {
	$group_types = array_map(
		function( $type ) {
			return [
				'label' => $type->get_label( 'plural' ),
				'value' => $type->get_slug(),
			];
		},
		cboxol_get_group_types()
	);

	cboxol_load_site_template_view( 'admin/edit-tags-group-types.php', [ 'group_types' => $group_types ] );
}
add_action( 'cboxol_template_category_add_form_fields', 'cboxol_site_template_add_group_type_fields_to_edit_tags' );

/**
 * Adds the group-type selecton interface on term.php.
 *
 * @param $term WP_Term object.
 * @return void
 */
function cboxol_site_template_add_group_type_fields_to_term( $term ) {
	$group_types = array_map(
		function( $type ) {
			return [
				'label' => $type->get_label( 'plural' ),
				'value' => $type->get_slug(),
			];
		},
		cboxol_get_group_types()
	);

	$selected = cboxol_get_term_group_types( $term->term_id );

	cboxol_load_site_template_view(
		'admin/term-group-types.php',
		[
			'group_types' => $group_types,
			'selected'    => $selected,
		]
	);
}
add_action( 'cboxol_template_category_edit_form', 'cboxol_site_template_add_group_type_fields_to_term' );

/**
 * Handles taxonomy edits and saves group types for site template categories.
 *
 * @param int $term_id ID of the term.
 * @return void
 */
function cboxol_site_template_category_handle_term_save( $term_id ) {
	if ( ! current_user_can( 'delete_sites' ) ) {
		return;
	}

	if ( ! isset( $_POST['cboxol-edit-term-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'cboxol_edit_term', 'cboxol-edit-term-nonce' );

	$submitted = $_POST['group-types'];

	// Delete existing and re-add.
	delete_term_meta( $term_id, 'cboxol_group_type' );

	$all_group_types = cboxol_get_group_types();
	foreach ( $submitted as $type ) {
		if ( ! isset( $all_group_types[ $type ] ) ) {
			continue;
		}

		add_term_meta( $term_id, 'cboxol_group_type', $type );
	}
}
add_action( 'saved_cboxol_template_category', 'cboxol_site_template_category_handle_term_save' );

/**
 * Adds 'Group Types' column to edit-tags.php for site template categories.
 *
 * @param array $columns
 * @return array
 */
function cboxol_site_template_categories_add_group_types_column( $columns ) {
	$columns['group_type'] = __( 'Group Type', 'commons-in-a-box' );
	return $columns;
}
add_filter( 'manage_edit-cboxol_template_category_columns', 'cboxol_site_template_categories_add_group_types_column' );

/**
 * Populates the 'Group Types' column on edit-tags.php for site template categories.
 */
function cboxol_site_template_categories_group_types_column_content( $content, $column, $term_id ) {
	$type_labels = cboxol_get_term_group_type_labels( $term_id );
	return implode( ', ', $type_labels );
}
add_filter( 'manage_cboxol_template_category_custom_column', 'cboxol_site_template_categories_group_types_column_content', 10, 3 );

/**
 * When a site is deleted, move its template into "Trash".
 *
 * @param \WP_Site $site Deleted site object.
 * @return void
 */
function cboxol_trash_site_template( $site ) {
	$switched    = false;
	$template_id = get_site_meta( $site->id, '_site_template_id', true );

	if ( ! $template_id ) {
		return;
	}

	if ( ! cbox_is_main_site() ) {
		switch_to_blog( cbox_get_main_site_id() );
		$switched = true;
	}

	wp_trash_post( $template_id );

	if ( $switched ) {
		restore_current_blog();
	}
}
add_action( 'wp_uninitialize_site', 'cboxol_trash_site_template', 20, 1 );

/**
 * Renders "Template Picker" panel on "Site Details" step.
 *
 * @return void
 */
function cboxol_render_template_picker() {
	$group_id = bp_get_current_group_id();
	$is_clone = (bool) cboxol_get_clone_source_group_id( $group_id );

	// Don't display template picker we're cloning the group.
	if ( $is_clone ) {
		return;
	}

	// Don't display template picker if there's no group type.
	$group_type = cboxol_get_group_group_type( $group_id );
	if ( ! $group_type || is_wp_error( $group_type ) ) {
		return;
	}

	// Don't display template picker if the group already has an associated site.
	$site_id       = openlab_get_site_id_by_group_id( $group_id );
	$external_site = openlab_get_external_site_url_by_group_id( $group_id );
	if ( $site_id || $external_site ) {
		return;
	}

	$categories = $group_type->get_site_template_categories();

	// If there are no valid categories, then there's nothing to show.
	if ( empty( $categories ) ) {
		return;
	}

	if ( is_wp_error( $categories ) ) {
		$categories = [];
	}

	$gloss = get_option( 'cboxol_stp_gloss', '' );

	wp_enqueue_style( 'cboxol-site-template-picker-style' );
	wp_enqueue_script( 'cboxol-site-template-picker-script' );

	cboxol_load_site_template_view(
		'template-picker.php',
		[
			'categories' => $categories,
			'gloss'      => $gloss,
		]
	);
}
add_action( 'openlab_after_group_site_markup', 'cboxol_render_template_picker' );

/**
 * Site Templates settings panel under Group Settings.
 *
 * @since 1.4.0
 */
function cboxol_site_templates_admin_page() {
	$templates_url = add_query_arg(
		'post_type',
		'cboxol_site_template',
		admin_url( 'edit.php' )
	);

	$categories_url = add_query_arg(
		[
			'post_type' => 'cboxol_site_template',
			'taxonomy'  => 'cboxol_template_category',
		],
		admin_url( 'edit-tags.php' )
	);

	$settings = [
		[
			'title'       => __( 'Site Templates', 'commons-in-a-box' ),
			'url'         => $templates_url,
			'description' => __( 'Create and manage site templates on the Site Templates admin page.', 'commons-in-a-box' ),
		],
		[
			'title'       => __( 'Site Template Categories', 'commons-in-a-box' ),
			'url'         => $categories_url,
			'description' => __( 'Create and manage site template categories on the Site Template Categories admin page.', 'commons-in-a-box' ),
		],
	];

	?>
	<div class="cboxol-admin-content">
		<p><?php esc_html_e( 'The Site Templates admin page allows the network administrator to create new site templates that can be assigned to different group types. Site Template Categories serve as the method for linking Site Templates to Group Types. When users create a new group with an associated site, they will see options to choose any of the site templates that have associated categories linked with that group type.', 'commons-in-a-box' ); ?></p>

		<?php cboxol_communication_settings_markup( $settings ); ?>
	</div>
	<?php
}

/**
 * Enqueue site-templates-admin JS on the site-templates admin page.
 *
 * @since 1.6.0
 *
 * @return void
 */
function cboxol_enqueue_site_templates_admin_scripts_on_edit() {
	if ( get_current_screen()->post_type === 'cboxol_site_template' ) {
		wp_enqueue_script( 'cboxol-site-template-picker-admin-script' );
		wp_enqueue_style( 'cboxol-site-template-picker-admin-style' );
	}
}
add_action( 'admin_enqueue_scripts', 'cboxol_enqueue_site_templates_admin_scripts_on_edit' );

/**
 * AJAX handler for site template drag-and-drop reordering.
 *
 * @since 1.6.0
 *
 * @return void
 */
function cboxol_site_templates_handle_post_order_update() {
	check_ajax_referer( 'wp_rest', 'security' );

	if ( ! current_user_can( 'edit_cboxol_site_templates' ) ) {
		return;
	}

	if ( ! isset( $_POST['order'] ) ) {
		wp_send_json_error( __( 'No order data found.', 'commons-in-a-box' ) );
	}

	$order_data = json_decode( stripslashes( $_POST['order'] ), true );

	foreach ( $order_data as $item ) {
		$item_order = isset( $item['position'] ) ? (int) $item['position'] : 0;
		$item_id    = isset( $item['id'] ) ? (int) $item['id'] : 0;

		if ( ! $item_order || ! $item_id ) {
			continue;
		}

		wp_update_post(
			[
				'ID'         => $item_id,
				'menu_order' => $item_order,
			]
		);
	}

	wp_send_json_success( __( 'Post order updated successfully.', 'commons-in-a-box' ) );
}
add_action( 'wp_ajax_cboxol_update_site_template_order', 'cboxol_site_templates_handle_post_order_update' );

/**
 * On edit.php?post_type=cboxol_site_template, force sort order by menu_order.
 *
 * @since 1.6.0
 *
 * @param \WP_Query $query
 * @return void
 */
function cboxol_site_templates_force_order_by_menu_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'cboxol_site_template' !== $query->get( 'post_type' ) ) {
		return;
	}

	$query->set( 'orderby', 'menu_order' );
	$query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'cboxol_site_templates_force_order_by_menu_order' );

/**
 * In the REST API, allow cboxol_site_template requests to have orderby=menu_order.
 *
 * @since 1.6.0
 *
 * @param array           $params
 * @param \WP_REST_Request $request
 * @return array
 */
function cboxol_site_templates_rest_api_allow_orderby_menu_order( $params, $request ) {
	$params['orderby']['enum'][] = 'menu_order';
	return $params;
}
add_filter( 'rest_cboxol_site_template_collection_params', 'cboxol_site_templates_rest_api_allow_orderby_menu_order', 10, 2 );

/**
 * Modifies site template REST requests to restrict based on template visibility settings.
 *
 * @since 1.6.0
 *
 * @param array            $args    Array of query arguments.
 * @param \WP_REST_Request $request The request object.
 * @return array
 */
function cboxol_site_templates_rest_api_restrict_visibility( $args, $request ) {
	$member_type_meta_query = [
		'relation'  => 'OR',
		'all_types' => [
			[
				'key'     => 'cboxol_limit_template_by_member_type',
				'compare' => 'NOT EXISTS',
			],
		],
	];

	$allowed_member_types = [ 0 ];
	if ( is_user_logged_in() ) {
		$current_member_type = cboxol_get_user_member_type( bp_loggedin_user_id() );

		if ( $current_member_type ) {
			$member_type_meta_query['limited_types'] = [
				'relation' => 'AND',
				[
					'key'     => 'cboxol_limit_template_by_member_type',
					'compare' => 'EXISTS',
				],
				[
					'key'   => 'cboxol_template_member_type',
					'value' => $current_member_type->get_slug(),
				],
			];
		}
	}

	$academic_unit_meta_query = [
		'relation'  => 'OR',
		'all_types' => [
			[
				'key'     => 'cboxol_limit_template_by_academic_unit',
				'compare' => 'NOT EXISTS',
			],
		],
	];

	$allowed_academic_units = [ 0 ];
	$current_group_id       = $request->get_param( 'group_id' );
	if ( $current_group_id ) {
		// Get the group's academic units.
		$group_units = cboxol_get_object_academic_units(
			[
				'object_type' => 'group',
				'object_id'   => $current_group_id,
			]
		);

		if ( $group_units ) {
			$group_unit_ids = array_map(
				function( $unit ) {
					return $unit->get_wp_post_id();
				},
				$group_units
			);

			$academic_unit_meta_query['limited_types'] = [
				'relation' => 'AND',
				[
					'key'     => 'cboxol_limit_template_by_academic_unit',
					'compare' => 'EXISTS',
				],
				[
					'key'   => 'cboxol_template_academic_unit',
					'value' => $group_unit_ids,
				],
			];
		}
	}

	$meta_query   = isset( $args['meta_query'] ) ? $args['meta_query'] : [];
	$meta_query[] = $member_type_meta_query;
	$meta_query[] = $academic_unit_meta_query;

	$args['meta_query'] = $meta_query;

	return $args;
}
add_filter( 'rest_cboxol_site_template_query', 'cboxol_site_templates_rest_api_restrict_visibility', 10, 2 );

/**
 * Manages columns on Site Templates list table panel.
 *
 * @since 1.6.0
 *
 * @param array $columns Columns.
 * @return array
 */
function cboxol_site_templates_manage_columns( $columns ) {
	$columns_to_add = [
		'visibility' => __( 'Visibility', 'commons-in-a-box' ),
	];

	// Insert before the 'date' column.
	$column_keys = array_keys( $columns );
	$date_index  = array_search( 'date', $column_keys, true );

	$new_column_keys = array_merge(
		array_slice( $column_keys, 0, $date_index, true ),
		array_keys( $columns_to_add ),
		array_slice( $column_keys, $date_index, null, true )
	);

	$new_columns = [];
	foreach ( $new_column_keys as $key ) {
		$column_value = isset( $columns_to_add[ $key ] ) ? $columns_to_add[ $key ] : $columns[ $key ];

		$new_columns[ $key ] = $column_value;
	}

	return $new_columns;
}
add_filter( 'manage_edit-cboxol_site_template_columns', 'cboxol_site_templates_manage_columns' );

/**
 * Handles the content of custom columns on Site Templates list table panel.
 *
 * @since 1.6.0
 *
 * @param string $column_name Column name.
 * @param int    $post_id     Post ID.
 * @return void
 */
function cboxol_site_templates_manage_custom_columns( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'visibility':
			$limit_by_member_types = cboxol_is_template_visibility_limited_by_member_type( $post_id );
			$selected_member_types = cboxol_get_template_member_types( $post_id );

			if ( $limit_by_member_types ) {
				$member_type_labels = array_map(
					function( $slug ) {
						$type = cboxol_get_member_type( $slug );
						return $type ? $type->get_label( 'singular' ) : '';
					},
					array_keys( $selected_member_types )
				);

				$member_types_text = implode( ', ', $member_type_labels );
			} else {
				$member_types_text = __( 'All', 'commons-in-a-box' );
			}

			$limit_by_academic_units = cboxol_is_template_visibility_limited_by_academic_unit( $post_id );
			$selected_academic_units = cboxol_get_template_academic_units( $post_id );

			if ( $limit_by_academic_units ) {
				$academic_unit_labels = array_map(
					function( $post_id ) {
						$post = get_post( $post_id );
						if ( ! $post || 'cboxol_acadunit' !== $post->post_type ) {
							return '';
						}

						$unit = cboxol_get_academic_unit( $post->post_name );
						return $unit ? $unit->get_name() : '';
					},
					array_keys( $selected_academic_units )
				);

				$academic_units_text = implode( ', ', array_filter( $academic_unit_labels ) );
			} else {
				$academic_units_text = __( 'All', 'commons-in-a-box' );
			}

			// translators: Member type labels.
			echo wp_kses_post( sprintf( __( 'Member Types: %s', 'commons-in-a-box' ), '<span class="visibility-values">' . $member_types_text . '</span>' ) );
			echo '<br />';

			// translators: Academic unit labels.
			echo wp_kses_post( sprintf( __( 'Academic Units: %s', 'commons-in-a-box' ), '<span class="visibility-values">' . $academic_units_text . '</span>' ) );

			break;
	}
}
add_action( 'manage_cboxol_site_template_posts_custom_column', 'cboxol_site_templates_manage_custom_columns', 10, 2 );
