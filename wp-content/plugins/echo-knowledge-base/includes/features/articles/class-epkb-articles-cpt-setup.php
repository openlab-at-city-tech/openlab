<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a new CUSTOM POST TYPE + category + tag for a given instance of KNOWLEDGE BASE.
 *
 * This KB articles will have their post_type set to this newly registered custom post type.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Articles_CPT_Setup {

	public function __construct() {

		add_action( 'init', array( $this, 'register_knowledge_base_post_types'), 10 );
		add_filter( 'post_type_link', array( $this, 'replace_linked_article_permalink' ), 10, 2 );

		if ( self::is_archive_on() ) {
			add_action( 'template_redirect', array( $this, 'archive_redirect' ) );
		}

		// only for front-end page display when categories are listed
		if ( ! defined('WP_ADMIN') ) {
			add_filter( 'the_category', array( $this, 'output_article_categories' ), 99, 3 );
		}
	}

	/**
	 * Read configuration and create configured custom post types, each representing Knowledge Base
	 */
	public function register_knowledge_base_post_types() {

		$current_id = EPKB_KB_Handler::get_current_kb_id();
		foreach ( epkb_get_instance()->kb_config_obj->get_kb_configs() as $kb_config ) {

			$result = self::register_custom_post_type( $kb_config, $current_id );
			if ( is_wp_error( $result ) ) {
				EPKB_Logging::add_log("Could not register custom post type.", $kb_config['id'], $result);
			}
		}

		// flush rules on plugin activation after CPTs were registered
		$is_flush_rewrite_rules = get_option( 'epkb_flush_rewrite_rules' );
		if ( ! empty( $is_flush_rewrite_rules ) && ! is_wp_error( $is_flush_rewrite_rules ) ) {
			delete_option( 'epkb_flush_rewrite_rules' );
			flush_rewrite_rules( false );
		}
	}

	/**
	 * Register custom post type, including taxonomies (category, tag) and other constructs.
	 *
	 * @param array $kb_config
	 * @param int|string $current_id
	 * @return bool|WP_Error
	 */
	public static function register_custom_post_type( $kb_config, $current_id ) {

		$kb_id = $kb_config['id'];

		// do not register Archived KB - except of KB Configuration admin page
		if ( $kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $kb_config['status'] ) && EPKB_Utilities::post( 'page' ) != 'epkb-kb-configuration' ) {
			return true;
		}

		$kb_post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		$kb_articles_common_path = empty( $kb_config['kb_articles_common_path'] ) ?
									EPKB_KB_Handler::get_default_slug( $kb_id ) : $kb_config['kb_articles_common_path'];

		// determine if this custom post type will be registered for user selected KB; if yes make it visible in admin UI
		$current_id = empty($current_id) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $current_id;
		$show_post_in_ui = ( $kb_id == $current_id ) || ! is_admin();  // true if front-end (like admin bar)

		// first we need to set up CATEGORY taxonomy so that its rules are above 'attachments' links from its post type

		/** setup Category taxonomy */

		/* translators: do NOT change this translation again. It will break links !!! */
		$category_slug = empty( $kb_config['category_slug'] ) ? _x( 'category', 'taxonomy singular name', 'echo-knowledge-base' ) : $kb_config['category_slug'];

		$category_taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$labels = [
				'name'              => _x( 'Categories', 'taxonomy general name', 'echo-knowledge-base' ),
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'echo-knowledge-base' ),
				'search_items'      => esc_html__( 'Search Categories', 'echo-knowledge-base' ),
				'all_items'         => esc_html__( 'All Categories', 'echo-knowledge-base' ),
				'parent_item'       => esc_html__( 'Parent Category', 'echo-knowledge-base' ),
				'parent_item_colon' => esc_html__( 'Parent Category:', 'echo-knowledge-base' ),
				'edit_item'         => esc_html__( 'Edit Category', 'echo-knowledge-base' ),
				'update_item'       => esc_html__( 'Update Category', 'echo-knowledge-base' ),
				'add_new_item'      => esc_html__( 'Add New Category', 'echo-knowledge-base' ),
				'new_item_name'     => esc_html__( 'New Category Name', 'echo-knowledge-base' ),
				'menu_name'         => esc_html__( 'Categories', 'echo-knowledge-base' ),
		];
		$args = [
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => $show_post_in_ui,
				'show_admin_column' => $show_post_in_ui,
				'show_in_nav_menus' => true,
				'query_var'         => $category_taxonomy_name,
				'show_in_rest'      => true,
				'rewrite'           => [
											'slug'         => $kb_articles_common_path . '/' . $category_slug,
											'with_front'   => false,
											'hierarchical' => true
										],
		];
		$result = register_taxonomy( $category_taxonomy_name, array( $kb_post_type ), $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** setup Tag taxonomy */
		$tag_taxonomy_name = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );
		$labels = [
				'name'                       => _x( 'Tags', 'taxonomy general name', 'echo-knowledge-base' ),
				'singular_name'              => _x( 'Tag', 'taxonomy singular name', 'echo-knowledge-base' ),
				'search_items'               => esc_html__( 'Search Tags', 'echo-knowledge-base' ),
				'all_items'                  => esc_html__( 'All Tags', 'echo-knowledge-base' ),
				'parent_item'                => esc_html__( 'Parent Tag', 'echo-knowledge-base' ),
				'parent_item_colon'          => esc_html__( 'Parent Tag:', 'echo-knowledge-base' ),
				'edit_item'                  => esc_html__( 'Edit Tag', 'echo-knowledge-base' ),
				'update_item'                => esc_html__( 'Update Tag', 'echo-knowledge-base' ),
				'view_item'                  => esc_html__( 'View Tag', 'echo-knowledge-base' ),
				'separate_items_with_commas' => esc_html__( 'Separate Tags with commas', 'echo-knowledge-base' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Tags', 'echo-knowledge-base' ),
				'add_new_item'               => esc_html__( 'Add New Tag', 'echo-knowledge-base' ),
				'new_item_name'              => esc_html__( 'New Tag Name', 'echo-knowledge-base' ),
				'menu_name'                  => esc_html__( 'Tags', 'echo-knowledge-base' )
		];
		$args = [
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => $show_post_in_ui,
				'show_admin_column'     => $show_post_in_ui,
				'show_in_nav_menus'     => true,
				'show_tagcloud'         => true,
				'query_var'             => $tag_taxonomy_name,
				'show_in_rest'          => true,
				'rewrite'               => [
												/* translators: do NOT change this translation again. It will break links !!! */
												'slug'         => $kb_articles_common_path . '/' . _x( 'tag', 'taxonomy singular name', 'echo-knowledge-base' ),
												'with_front'   => false,
												'hierarchical' => false
											],
		];
		$result = register_taxonomy( $tag_taxonomy_name, array( $kb_post_type ), $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** setup Custom Post Type */
		$categories_in_url = self::is_category_in_url( $kb_config );
		$post_type_name = _x( $kb_config['kb_name'], 'post type general name', 'echo-knowledge-base' );
		$post_type_name = empty( $post_type_name ) ? 'Knowledge Base' : $post_type_name;
		$labels = [
				'name'               => $post_type_name,
				'singular_name'      => $post_type_name . ' - ' . _x( 'Article', 'post type singular name', 'echo-knowledge-base' ),
				'menu_name'          => _x( 'Knowledge Base', 'admin menu', 'echo-knowledge-base' ),
				'add_new'            => _x( 'Add New Article', 'Articles', 'echo-knowledge-base' ),
				'add_new_item'       => esc_html__( 'Add New Article', 'echo-knowledge-base' ),
				'edit_item'          => esc_html__( 'Edit Article', 'echo-knowledge-base' ),
				'new_item'           => esc_html__( 'New Article', 'echo-knowledge-base' ),
				'all_items'          => esc_html__( 'All Articles', 'echo-knowledge-base' ),
				'view_item'          => esc_html__( 'View Article', 'echo-knowledge-base' ),
				'search_items'       => esc_html__( 'Search in Articles', 'echo-knowledge-base' ),
				'not_found'          => esc_html__( 'No Articles found', 'echo-knowledge-base' ),
				'not_found_in_trash' => esc_html__( 'No Articles found in Trash', 'echo-knowledge-base' ),
				'parent_item_colon'  => '',
		];
		$args = [
				'labels'             => $labels,
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => $show_post_in_ui,
				'publicly_queryable' => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $kb_articles_common_path . ( $categories_in_url ? '/%category%' : '' ), 'with_front' => false ), // do not translate
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'has_archive'        => self::is_archive_on() ? $kb_articles_common_path . '-archive' : false,   // see OneNote for details
				'hierarchical'       => false,
				'show_in_rest'       => true,
				'menu_position'      => 5,    // below Posts menu
				'menu_icon'          => 'dashicons-welcome-learn-more',
				'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author', 'comments', 'custom-fields'],
		];
		$result = register_post_type( $kb_post_type, $args );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** tie taxonomies to the post type */

		$result = register_taxonomy_for_object_type( $category_taxonomy_name, $kb_post_type );
		if ( ! $result ) {
			return new WP_Error( 'register_object_for_tax_failed', "Failed to register taxonomy '$category_taxonomy_name' for post type '$kb_post_type' for KB ID: $kb_id" );
		}

		$result = register_taxonomy_for_object_type( $tag_taxonomy_name, $kb_post_type );
		if ( ! $result ) {
			return new WP_Error( 'register_object_for_tax_failed', "Failed to register taxonomy '$tag_taxonomy_name' for post type '$kb_post_type' for KB ID: $kb_id" );
		}

		return true;
	}

	/**
	 * If configured to include category in article permalinks then makes that happen here.
	 *
	 * @param $permalink
	 * @param $post
	 *
	 * @return mixed
	 * @noinspection PhpUnused
	 */
	public function replace_linked_article_permalink( $permalink, $post ) {

		// only handle our articles
		if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return $permalink;
		}

		// handle all posts except onces that are not ready to have slug altered
		$draft_or_pending = isset($post->post_status) && in_array($post->post_status, array('trash','inherit','auto-draft'), true );
		if ( $draft_or_pending ) {
			return $permalink;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error( $kb_id ) ) {
			return $permalink;
		}

		// only if KB config has set option to include categories in URL continue
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		if ( ! self::is_category_in_url( $kb_config ) ) {
			return $permalink;
		}

		$kb_taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );

		$default_category = get_term( get_option( 'default_category' ), 'category' );
		$default_category = empty( $default_category ) || is_wp_error( $default_category ) ? _x( 'uncategorized', 'do not change again', 'echo-knowledge-base' ) : $default_category->slug;

		// retrieve all post categories
		$categories = get_the_terms( $post->ID, $kb_taxonomy_name );
		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			return str_replace( '%category%' , $default_category , $permalink );
		}

		// invoke filter on selected first category
		$category_object = apply_filters( 'post_link_category', $categories[0], $categories, $post );
		if ( empty( $category_object ) || is_wp_error( $category_object ) ) {
			return str_replace( '%category%' , $default_category , $permalink );
		}

		// find category hierarchy slug
		$category_object = get_term( $category_object, 'category' );
		if ( empty( $category_object->slug ) || is_wp_error( $category_object ) ) {
			return str_replace( '%category%' , $default_category , $permalink );
		}

		// add parent slugs if any
		$category_slug = $category_object->slug;

		if ( $category_object->parent ) {

			$parent = $category_object->parent;
			$args = array(
				'separator' => '/',
				'link'      => false,
				'format'    => 'slug',
			);
			$parents_slug = get_term_parents_list( $parent, $kb_taxonomy_name, $args );
			if ( empty( $parents_slug ) || is_wp_error( $parents_slug ) ) {
				return str_replace( '%category%' , $default_category , $permalink );
			}

			$category_slug = $parents_slug . $category_slug;
		}

		// show default category in permalinks, without having to assign it explicitly.
		if ( empty( $category_slug ) ) {
			$category_slug = $default_category;
		}

		// add proper category slug
		$permalink = str_replace( '%category%' , $category_slug , $permalink );

		return $permalink;
	}

	/**
	 *  Filters the category or list of categories.
	 *
	 * @param string  $thelist   List of categories for the current post.
	 * @param string $separator Separator used between the categories.
	 * @param string $parents   How to display the category parents. Accepts 'multiple',
	 *                          'single', or empty.
	 *
	 * @return mixed
	 */
	public function output_article_categories( $thelist, $separator=', ', $parents='' ) {
		global $wp_rewrite;

		// for some strange reason the same hook has only 1 parameter in wp-admin
		$post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : '';
		if ( defined( 'WP_ADMIN' ) || empty( $post ) || ! $post instanceof WP_Post || ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return $thelist;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error( $kb_id ) ) {
			return $thelist;  // shouldn't happen because of is_kb_post_type() above
		}

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
		$separator = ! empty( $separator ) && is_string( $separator ) ? $separator : ', ';

		// find all categories of this article including subcategories
		$ix = 0;
		foreach ( $articles_seq_data as $category_id => $sub_category_article_list ) {
			if ( isset( $sub_category_article_list[ $post->ID]) && isset( $sub_category_article_list[0]) ) {
				$thelist .= ( $ix++ == 0 ? '' : $separator ) . '<a href="' . esc_url( EPKB_Utilities::get_term_url( $category_id ) ) . '" ' . $rel . '>' . $sub_category_article_list[0] . '</a>';
			}
		}

		return $thelist;
	}

	/**
	 * Determine if KB Category name appears in article URL
	 * @param $kb_config
	 * @return bool
	 */
	public static function is_category_in_url( $kb_config ) {
		return ! empty($kb_config['categories_in_url_enabled']) && $kb_config['categories_in_url_enabled'] == 'on';
	}

	private static function is_archive_on() {
		// for now enable only for Elementor users
		return EPKB_Site_Builders::is_elementor_enabled();
	}

	/**
	 * Redirect from KB CPT Archive Page to the KB Main Page
	 */
	public static function archive_redirect() {
		global $wp_query;

		if ( ! self::is_archive_on() ) {
			return;
		}

		if ( ! is_post_type_archive() ) {
			return;
		}

		if ( empty( $wp_query->query ) || ! is_array( $wp_query->query ) || empty( $wp_query->query['post_type'] ) ) {
			return;
		}
		
		if ( ! EPKB_KB_Handler::is_kb_post_type( $wp_query->query['post_type'] )  ) {
			return;
		}
		
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $wp_query->query['post_type'] );
		if ( empty($kb_id) || is_wp_error($kb_id) ) {
			return;
		}
		
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			return;
		}
		
		$url = empty( $kb_config['kb_articles_common_path'] ) ? home_url() : home_url( $kb_config['kb_articles_common_path'] );
		wp_redirect( $url, '301', 'Echo Knowledge Base' );
		exit();
	}
}

