<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle operations on knowledge base such as adding, deleting and updating KB
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Handler {

	// name of KB shortcode
	const KB_MAIN_PAGE_SHORTCODE_NAME = 'epkb-knowledge-base'; // changing this requires db update

	// Prefix for custom post type name associated with given KB; this will never change
	const KB_POST_TYPE_PREFIX = 'epkb_post_type_';  // changing this requires db update
	const KB_CATEGORY_TAXONOMY_SUFFIX = '_category';  // changing this requires db update; do not translate
	const KB_TAG_TAXONOMY_SUFFIX = '_tag'; // changing this requires db update; do not translate

	/**
	 * Create a new Knowledge Base using default configuration when:
	 *  a) plugin is installed and activated
	 *  b) user clicks on 'Add Knowledge Base' button (requires Unlimited KBs add-on)
	 * First default knowledge base has name 'Knowledge Base' with ID 1
	 * Add New KB will create KB with pre-set name 'Knowledge Base 2' with ID 2 and so on.
	 *
	 * @param int $new_kb_id - ID of the new KB
	 * @param string $new_kb_main_page_title
	 * @param string $new_kb_main_page_slug
	 * @param $kb_main_page_layout
	 * @return array|WP_Error - the new KB configuration or WP_Error
	 */
	public static function add_new_knowledge_base( $new_kb_id, $new_kb_main_page_title='', $new_kb_main_page_slug='', $kb_main_page_layout='' ) {

		// use default KB configuration for a new KB
		EPKB_Logging::disable_logging();

		// use default KB configuration ONLY if none exists
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $new_kb_id );

		// 1. Add first KB Main page if none exists; first KB is just called Knowledge Base
		$kb_main_pages = $kb_config['kb_main_pages'];
		if ( empty( $kb_main_pages ) ) {

			$post = self::create_kb_main_page( $new_kb_id, $new_kb_main_page_title, $new_kb_main_page_slug );
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			$kb_config['kb_name'] = $post->post_title;
			$kb_main_pages[ $post->ID ] = $post->post_title;
			$kb_config['kb_main_pages'] = $kb_main_pages;
			$kb_config['kb_articles_common_path'] = urldecode( sanitize_title_with_dashes( $post->post_name, '', 'save' ) );
		}

		// 2. create default empty sequence if none exists ( setup wizard can be launched multiple times, no need to overwrite existing)
		$default_categories_data = EPKB_Utilities::get_kb_option( $new_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( $default_categories_data === null ) {
			EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, [] );
		}

		// create default empty sequence if none exists ( setup wizard can be launched multiple times, no need to overwrite existing)
		$default_articles_data = EPKB_Utilities::get_kb_option( $new_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( $default_articles_data === null ) {
			EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, [] );
		}

		// 3. Add a sample category and sub-category with article each if no category exists (only default KB)
		$all_kb_terms = EPKB_Core_Utilities::get_kb_categories_unfiltered( $new_kb_id );
		if ( $new_kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID && empty( $all_kb_terms ) ) {
			EPKB_KB_Demo_Data::create_sample_categories_and_articles( $new_kb_id, $kb_main_page_layout );
		}

		if ( $new_kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ) {
			EPKB_KB_Demo_Data::create_sample_faqs( $new_kb_id );
		}

		// 4. save new/updated KB configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $new_kb_id, $kb_config );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// let add-ons know we have a new KB; default KB is created when each add-on is activated
		if ( $new_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID ) {
			do_action( 'eckb_new_knowledge_base_added', $new_kb_id );
		}

		// mark the current KB to indicate that Setup Wizard was not completed for it
		EPKB_Utilities::save_wp_option( 'epkb_not_completed_setup_wizard_' . $new_kb_id, true );

		return $kb_config;
	}

	/**
	 * Create a new KB main page
	 *
	 * @param $kb_id
	 * @param string $kb_main_page_title
	 * @param string $kb_main_page_slug
	 * @param bool $use_kb_blocks - determines whether to use blocks or shortcode TODO: set to default true on blocks release
	 * @param array $kb_blocks - list of block configs in desired sequence
	 * @return WP_Error|WP_Post
	 */
	public static function create_kb_main_page( $kb_id, $kb_main_page_title, $kb_main_page_slug, $use_kb_blocks = false, $kb_blocks = array() ) {

		// we add new KB Page here so remove hook
		remove_filter('save_post', 'epkb_add_main_page_if_required', 10 );

		// do not process KB shortcode during KB creation
		remove_shortcode( EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME );

		$shortcode = '[' . self::KB_MAIN_PAGE_SHORTCODE_NAME . ' id=' . $kb_id . ']';

		// case: block Theme
		if ( EPKB_Utilities::is_block_theme() ) {

			// case: KB blocks
			if ( $use_kb_blocks ) {

				// if required to create the page via KB blocks but no blocks were specified, then create the default set of blocks
				$default_block_attributes = json_encode( array(
					'kb_id' => $kb_id,
				) );

				if ( empty( $kb_blocks ) ) {
					$kb_blocks = array(
						array(
							'name' => EPKB_Search_Block::EPKB_BLOCK_NAME,
							'attributes' => $default_block_attributes,
						),
						array(
							'name' => EPKB_Basic_Layout_Block::EPKB_BLOCK_NAME,
							'attributes' => $default_block_attributes,
						),
					);
				}

				$post_content = '';
				foreach ( $kb_blocks as $one_block_config ) {
					$block_attributes = empty( $one_block_config['attributes'] ) ? $default_block_attributes : $one_block_config['attributes'];
					$post_content .= '<!-- wp:' . EPKB_Abstract_Block::EPKB_BLOCK_NAMESPACE . '/' . $one_block_config['name'] . ' ' . $block_attributes . ' /-->';
				}

			// case: WordPress block with KB shortcode
			} else {
				$post_content = '<!-- wp:shortcode -->' . $shortcode . '<!-- /wp:shortcode -->';
			}

		// case: non-block Theme
		} else {
			$post_content = $shortcode;
		}

		$kb_main_page_title = empty( $kb_main_page_title ) ? esc_html__( 'Knowledge Base', 'echo-knowledge-base' ) : $kb_main_page_title;
		$my_post = array(
			'post_title'    => $kb_main_page_title,
			'post_name'     => $kb_main_page_slug,
			'post_type'     => 'page',
			'post_content'  => $post_content,
			'post_status'   => 'publish',
			'comment_status' => 'closed'
			// current user or 'post_author'   => 1,
		);
		$post_id = wp_insert_post( $my_post );
		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			return is_wp_error( $post_id ) ? $post_id : new WP_Error( 'E01', 'Could not insert KB Main Page with slug ' . $kb_main_page_slug );
		}

		$post = WP_Post::get_instance( $post_id );
		if ( empty( $post ) ) {
			return new WP_Error( 'E02', 'Could not find post with id ' . $post_id );
		}

		return $post;
	}

	/**
	 * Get KB slug based on default KB name and ID. Default KB has slug without ID.
	 *
	 * @param $kb_id
	 *
	 * @return string
	 */
	public static function get_default_slug( $kb_id ) {
		/* translators: do NOT change this translation again. It will break links !!! */
		return sanitize_title_with_dashes( _x( 'Knowledge Base', 'slug', 'echo-knowledge-base' ) . ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : '-' . $kb_id ) );
	}

	/**
	 * Retrieve current KB ID based on post_type value in URL based on user request etc.
	 *
	 * @return String|int empty if not found
	 */
	public static function get_current_kb_id() {
		global $eckb_kb_id;

		if ( ! empty( $eckb_kb_id ) ) {
			return $eckb_kb_id;
		}

		// 1. retrieve current post being used and if user selected a tab for specific KB
		$kb_id = self::find_current_kb_id();
		if ( empty( $kb_id ) || ( $kb_id instanceof WP_Error ) || ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return '';
		}

		// 2. check if the "current id" belongs to one of the existing KBs
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID ) {
			$db_kb_config = new EPKB_KB_Config_DB();
			$kb_ids = $db_kb_config->get_kb_ids();
			if ( ! in_array( $kb_id, $kb_ids ) ) {
				return '';
			}
		}

		$eckb_kb_id = $kb_id;

		return $kb_id;
	}

	/**
	 * Find current KB ID
	 *
	 * @return string | int | WP_Error
	 */
	private static function find_current_kb_id() {
		global $current_screen;

		// try to find KB ID from post_type
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$kb_post_type = empty( $_REQUEST['post_type'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'post_type' ) );
		if ( ! empty( $kb_post_type ) && strpos( $kb_post_type, self::KB_POST_TYPE_PREFIX ) !== false ) {
			return self::get_kb_id_from_post_type( $kb_post_type );
		}

		// try to find KB ID from taxonomy
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$epkb_taxonomy = empty( $_REQUEST['taxonomy'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'taxonomy' ) );
		if ( ! empty($epkb_taxonomy) && strpos( $epkb_taxonomy, self::KB_POST_TYPE_PREFIX ) !== false ) {

			// if is KB category
			if ( strpos( $epkb_taxonomy, self::KB_CATEGORY_TAXONOMY_SUFFIX ) !== false ) {
				return self::get_kb_id_from_category_taxonomy_name( $epkb_taxonomy );
			}

			// if is KB tag
			if ( strpos( $epkb_taxonomy, self::KB_TAG_TAXONOMY_SUFFIX ) !== false ) {
				return self::get_kb_id_from_tag_taxonomy_name( $epkb_taxonomy );
			}
		}

		// try to find KB ID from post_type in the current screen
		if ( ! empty($current_screen->post_type) && strpos( $current_screen->post_type, self::KB_POST_TYPE_PREFIX ) !== false ) {
			return self::get_kb_id_from_post_type( $current_screen->post_type );
		}

		// try to find KB ID from action - e.g. when adding category within KB article
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$epkb_action = empty( $_REQUEST['action'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'action' ) );
		if ( ! empty( $epkb_action ) && strpos( $epkb_action, self::KB_POST_TYPE_PREFIX ) !== false ) {
			$found_taxonomy_name = str_replace( 'add-', '', $epkb_action );
			$found_kb_id = EPKB_KB_Handler::get_kb_id_from_category_taxonomy_name( $found_taxonomy_name );
			if ( ! is_wp_error( $found_kb_id ) ) {
				return $found_kb_id;
			}
		}

		// try to find KB ID from epkb_kb_id
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$epkb_kb_id = empty( $_REQUEST['epkb_kb_id'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'epkb_kb_id' ) );
		if ( ! empty( $epkb_kb_id ) && EPKB_Utilities::is_positive_int( $epkb_kb_id )) {
			return $epkb_kb_id;
		}

		// try to find KB ID from Setup Wizard call (on apply changes)
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$epkb_wizard_kb_id = empty( $_REQUEST['epkb_wizard_kb_id'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'epkb_wizard_kb_id' ) );
		if ( ! empty( $epkb_wizard_kb_id ) && EPKB_Utilities::is_positive_int( $epkb_wizard_kb_id ) ) {
			return $epkb_wizard_kb_id;
		}

		// try to find KB ID from the Editor AJAX call (on apply changes)
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$epkb_editor_kb_id = empty( $_REQUEST['epkb_editor_kb_id'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'epkb_editor_kb_id' ) );
		if ( ! empty( $epkb_editor_kb_id ) && EPKB_Utilities::is_positive_int( $epkb_editor_kb_id ) ) {
			return $epkb_editor_kb_id;
		}

		// try to find KB ID from post - when editing article
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$epkb_post_id = empty( $_REQUEST['post'] ) ? '' : preg_replace( '/\D/', '', EPKB_Utilities::request_key( 'post' ) );
		if ( ! empty( $epkb_action ) && $epkb_action == 'edit' && ! empty( $epkb_post_id ) && EPKB_Utilities::is_positive_int( $epkb_post_id ) ) {
			$post = EPKB_Core_Utilities::get_kb_post_secure( $epkb_post_id );
			if ( ! empty( $post ) ) {
				return self::get_kb_id_from_post_type( $post->post_type );
			}
		}

		// REST API
		$request_uri = empty( $_SERVER['REQUEST_URI'] ) ? '' : sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		if ( ! empty( $request_uri ) && strpos( $request_uri, '/wp-json/wp/' ) !== false && strpos( $request_uri, '/' . self::KB_POST_TYPE_PREFIX ) !== false ) {
			return self::get_kb_id_from_rest_endpoint( $request_uri );
		}

		return '';
	}

	/**
	 * Is this KB post type?
	 *
	 * @param $post_type
	 * @return bool
	 */
	public static function is_kb_post_type( $post_type ) {
		if ( empty( $post_type ) || ! is_string( $post_type ) ) {
			return false;
		}
		// we are only interested in KB articles
		return strncmp( $post_type, self::KB_POST_TYPE_PREFIX, strlen( self::KB_POST_TYPE_PREFIX ) ) == 0;
	}

	/**
	 * Is this KB taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_kb_taxonomy( $taxonomy ) {
		if ( empty( $taxonomy ) || ! is_string( $taxonomy ) ) {
			return false;
		}

		// we are only interested in KB articles
		return strncmp( $taxonomy, self::KB_POST_TYPE_PREFIX, strlen( self::KB_POST_TYPE_PREFIX ) ) == 0;
	}

	/**
	 * Is this KB Category taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_kb_category_taxonomy( $taxonomy ) {
		if ( empty( $taxonomy ) || ! is_string( $taxonomy ) ) {
			return false;
		}

		// we are only interested in KB articles
		return strncmp( $taxonomy, self::KB_POST_TYPE_PREFIX, strlen( self::KB_POST_TYPE_PREFIX ) ) == 0 && strpos( $taxonomy, self::KB_CATEGORY_TAXONOMY_SUFFIX ) !== false;
	}

	/**
	 * Is this KB Tag taxonomy?
	 *
	 * @param $taxonomy
	 * @return bool
	 */
	public static function is_kb_tag_taxonomy( $taxonomy ) {
		if ( empty( $taxonomy ) || ! is_string( $taxonomy ) ) {
			return false;
		}

		return strncmp( $taxonomy, self::KB_POST_TYPE_PREFIX, strlen( self::KB_POST_TYPE_PREFIX ) ) == 0 && strpos( $taxonomy, self::KB_TAG_TAXONOMY_SUFFIX ) !== false;
	}

	/**
	 * Does request have KB taxonomy or post type ?
	 *
	 * @return bool
	 */
	public static function is_kb_request() {

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$kb_post_type = empty( $_REQUEST['post_type'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'post_type' ) );
		$is_kb_post_type = !empty( $kb_post_type ) && self::is_kb_post_type( $kb_post_type );
		if ( $is_kb_post_type ) {
			return true;
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$kb_taxonomy = empty( $_REQUEST['taxonomy'] ) ? '' : preg_replace( '/[^A-Za-z0-9 \-_]/', '', EPKB_Utilities::request_key( 'taxonomy' ) );
		$is_kb_taxonomy = !empty( $kb_taxonomy ) && self::is_kb_taxonomy( $kb_taxonomy );

		return $is_kb_taxonomy;
	}

	/**
	 * Retrieve current KB post type based on post_type value in URL based on user request etc.
	 *
	 * @return String empty if valid post type not found
	 */
	public static function get_current_kb_post_type() {
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return '';
		}
		return self::get_post_type( $kb_id );
	}

	/**
	 * Retrieve KB post type name e.g. ep kb_post_type_1
	 *
	 * @param $kb_id - assumed valid id
	 *
	 * @return string
	 */
	public static function get_post_type( $kb_id ) {
		$kb_id = EPKB_Utilities::sanitize_int($kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );
		return self::KB_POST_TYPE_PREFIX . $kb_id;
	}

	/**
	 * Retrieve KB post type name e.g. <post type>_1
	 *
	 * @return string empty when kb id cannot be determined
	 */
	public static function get_post_type2() {
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return '';
		}
		return self::KB_POST_TYPE_PREFIX . $kb_id;
	}

	/**
	 * Return category name e.g. ep kb_post_type_1_category
	 *
	 * @param $kb_id - assumed valid id
	 *
	 * @return string
	 */
	public static function get_category_taxonomy_name( $kb_id ) {
		return self::get_post_type( $kb_id ) . self::KB_CATEGORY_TAXONOMY_SUFFIX;
	}

	/**
	 * Return category name e.g. <post type>_1_category
	 *
	 * @return string empty when kb id cannot be determined
	 */
	public static function get_category_taxonomy_name2() {
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return '';
		}
		return self::get_post_type( $kb_id ) . self::KB_CATEGORY_TAXONOMY_SUFFIX;
	}

	/**
	 * Return tag name e.g. ep kb_post_type_1_tag
	 *
	 * @param $kb_id - assumed valid id
	 *
	 * @return string
	 */
	public static function get_tag_taxonomy_name( $kb_id ) {
		return self::get_post_type( $kb_id ) . self::KB_TAG_TAXONOMY_SUFFIX;
	}

	/**
	 * Retrieve KB ID from tag or category taxonomy.
	 * @param $taxonomy
	 * @return bool|int|WP_Error
	 */
	public static function get_kb_id_from_any_taxonomy( $taxonomy ) {
		$kb_id = self::get_kb_id_from_category_taxonomy_name( $taxonomy );
		if ( is_wp_error( $kb_id ) ) {
			$kb_id = self::get_kb_id_from_tag_taxonomy_name( $taxonomy );
			if ( is_wp_error( $kb_id ) ) {
				return new WP_Error('49', "kb_id not found");
			}
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from category taxonomy name
	 *
	 * @param $category_name
	 *
	 * @return int|WP_Error
	 */
	public static function get_kb_id_from_category_taxonomy_name( $category_name ) {

		if ( empty( $category_name ) || ! is_string( $category_name )
			|| strpos( $category_name, self::KB_POST_TYPE_PREFIX ) === false || strpos( $category_name, self::KB_CATEGORY_TAXONOMY_SUFFIX ) === false ) {
			return new WP_Error('40', "kb_id not found");
		}

		$kb_id = str_replace( array( self::KB_POST_TYPE_PREFIX, self::KB_CATEGORY_TAXONOMY_SUFFIX ), '', $category_name );

		if ( empty( $kb_id ) ) {
			return new WP_Error('41', "kb_id not found");
		}

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return new WP_Error('42', "kb_id not valid");
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from tag taxonomy name
	 *
	 * @param $tag_name
	 *
	 * @return int | WP_Error
	 */
	public static function get_kb_id_from_tag_taxonomy_name( $tag_name ) {

		if ( empty($tag_name) || ! is_string($tag_name)
			|| strpos( $tag_name, self::KB_POST_TYPE_PREFIX ) === false || strpos( $tag_name, self::KB_TAG_TAXONOMY_SUFFIX ) === false ) {
			return new WP_Error('50', "kb_id not found");
		}

		$kb_id = str_replace( array( self::KB_POST_TYPE_PREFIX, self::KB_TAG_TAXONOMY_SUFFIX ), '', $tag_name );

		if ( empty($kb_id) ) {
			return new WP_Error( '51', "kb_id not found" );
		}

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return new WP_Error( '52', "kb_id not valid" );
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from article type name
	 *
	 * @param String $post_type is post or post type
	 *
	 * @return int|WP_Error if no kb_id found
	 */
	public static function get_kb_id_from_post_type( $post_type ) {

		if ( empty( $post_type ) || ! is_string( $post_type ) || strpos( $post_type, self::KB_POST_TYPE_PREFIX ) === false ) {
			return new WP_Error('35', "kb_id not found");
		}

		$kb_id = str_replace( self::KB_POST_TYPE_PREFIX, '', $post_type );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return new WP_Error('36', "kb_id not valid");
		}

		return $kb_id;
	}

	/**
	 * Retrieve KB ID from REST API
	 * @param $endpoint
	 * @return int|WP_Error
	 */
	public static function get_kb_id_from_rest_endpoint( $endpoint ) {

		$parts = explode('?', $endpoint);
		if ( empty($parts) ) {
			return new WP_Error('37', "kb_id not valid");
		}

		$parts = explode('/', $parts[0]);
		if ( empty($parts) ) {
			return new WP_Error('37', "kb_id not valid");
		}

		$kb_id = new WP_Error('38', "kb_id not valid");
		foreach( $parts as $part ) {
			if ( ! self::is_kb_post_type( $part ) ) {
				continue;
			}

			if ( strpos( $part, self::KB_CATEGORY_TAXONOMY_SUFFIX ) !== false ) {
				$kb_id = self::get_kb_id_from_category_taxonomy_name( $part );
				break;
			} else if ( strpos( $part, self::KB_TAG_TAXONOMY_SUFFIX ) !== false ) {
				$kb_id = self::get_kb_id_from_tag_taxonomy_name( $part );
				break;
			} else {
				$kb_id = self::get_kb_id_from_post_type( $part );
				break;
			}
		}

		return $kb_id;
	}

	/**
	 * Determine if the current page is KB main page i.e. it contains KB shortcode or KB layout block and return its KB ID if any
	 * @param null $the_post - either pass post to the method or use current post
	 * @return int|null return KB ID if current page is KB main page otherwise null
	 */
	public static function get_kb_id_from_kb_main_page( $the_post=null ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$global_post = empty( $GLOBALS['post'] ) ? '' : $GLOBALS['post'];
		$found_post = empty( $the_post ) ? $global_post : $the_post;
		if ( empty( $found_post ) || ! isset( $found_post->post_content ) || empty( $found_post->ID ) ) {
			return null;
		}

		// search for KB Main Page block - the block attributes can be empty if all of them have default value (use default KB id then)
		$block_attributes = EPKB_Block_Utilities::parse_block_attributes_from_post( $found_post, '-layout' );
		if ( $block_attributes !== false ) {
			return isset( $block_attributes['kb_id'] ) ? $block_attributes['kb_id'] : EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// ensure WP knows about the shortcode
		add_shortcode( self::KB_MAIN_PAGE_SHORTCODE_NAME, array( 'EPKB_Layouts_Setup', 'output_kb_page_shortcode' ) );

		// find shortcode in post content or meta data
		if ( has_shortcode( $found_post->post_content, self::KB_MAIN_PAGE_SHORTCODE_NAME ) ) {
			$content = $found_post->post_content;
		} else {
			$content = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta " .
			                           "WHERE post_id = %d AND meta_value LIKE %s", $found_post->ID, '%' . $wpdb->esc_like( self::KB_MAIN_PAGE_SHORTCODE_NAME ) . '%' ) );
		}

		// does page have KB shortcode?
		if ( empty( $content ) ) {
			return null;
		}

		$kb_id = self::get_shortcode_default( $content );
		if ( empty( $kb_id ) ) {
			$kb_id = self::get_shortcode_custom( $content );
			if ( empty( $kb_id) ) {
				$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
			}
		}

		return $kb_id;
	}

	private static function get_shortcode_default( $content ) {

		if ( ! preg_match_all( '/' . get_shortcode_regex( [self::KB_MAIN_PAGE_SHORTCODE_NAME] ) . '/s', $content, $matches, PREG_SET_ORDER ) ) {
			return null;
		}

		// get KB ID from the shortcode
		foreach ( $matches as $shortcode ) {
			$attributes = shortcode_parse_atts( $shortcode[3] );

			// shortcode may not have the 'id' attribute, then use default id
			if ( empty( $attributes['id'] ) ) {
				return EPKB_KB_Config_DB::DEFAULT_KB_ID;
			}

			$kb_id = $attributes['id'];
			if ( EPKB_Utilities::is_positive_int( $kb_id ) ) {
				return (int)$kb_id;
			}
		}

		return null;
	}

	private static function get_shortcode_custom( $content ) {

		$start = strpos( $content, self::KB_MAIN_PAGE_SHORTCODE_NAME );
		if ( empty( $start ) || $start < 0 ) {
			return null;
		}

		$end = strpos( $content, ']', $start );
		if ( empty( $end ) || $end < 1 ) {
			return null;
		}

		$shortcode = substr( $content, $start, $end );
		if ( empty( $shortcode ) || strlen( $shortcode ) < strlen( self::KB_MAIN_PAGE_SHORTCODE_NAME ) ) {
			return null;
		}

		preg_match_all('!\d+!', $shortcode, $number);
		$number = empty($number[0][0]) ? 0 : $number[0][0];
		if ( ! EPKB_Utilities::is_positive_int( $number ) ) {
			return null;
		}

		return (int)$number;
	}

	/**
	 * Return all KB Main pages that we know about. Also remove old ones.
	 *
	 * @param $kb_config
	 * @return array a list of KB Main Pages titles and links
	 */
	public static function get_kb_main_pages( $kb_config ) {

		$kb_main_pages = $kb_config['kb_main_pages'];
		$kb_main_pages_info = array();
		foreach ( $kb_main_pages as $post_id => $post_title ) {

			$post_status = get_post_status( $post_id );

			// remove previous page versions
			if ( empty( $post_status ) || $post_status == 'inherit' || $post_status == 'trash' ) {
				unset( $kb_main_pages[ $post_id ] );
				continue;
			}

			$post = get_post( $post_id );
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				unset( $kb_main_pages[ $post_id ] );
				continue;
			}

			// remove page that does not contain KB shortcode or KB layout block anymore
			$kb_id = self::get_kb_id_from_kb_main_page( $post );
			if ( empty( $kb_id ) || $kb_id != $kb_config['id'] ) {
				unset( $kb_main_pages[ $post_id ] );
				continue;
			}

			$kb_post_slug = get_page_uri( $post_id );  // includes PARENT directory slug
			if ( is_wp_error( $kb_post_slug ) || empty( $kb_post_slug ) || is_array( $kb_post_slug ) ) {
				$kb_post_slug = EPKB_KB_Handler::get_default_slug( $kb_id );
			}

			$kb_main_pages_info[$post_id] = array( 'post_title' => $post_title, 'post_status' => EPKB_Utilities::get_post_status_text( $post_status ), 'post_slug' => urldecode($kb_post_slug) );
		}

		// we need to remove pages that are revisions
		if ( count( array_diff_key( $kb_config['kb_main_pages'], $kb_main_pages ) ) > 0 ) {
			$kb_config['kb_main_pages'] = $kb_main_pages;
			epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );  // ignore error for now
		}

		return $kb_main_pages_info;
	}

	/**
	 * Find KB Main Page that is not in trash and get its URL (page that matches kb_articles_common_path in KB config or first main page URL).
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_first_kb_main_page_url( $kb_config ) {

		$first_page_url = '';
		$kb_main_pages = $kb_config['kb_main_pages'];

		foreach ( $kb_main_pages as $post_id => $post_title ) {

			if ( empty( $post_id ) ) {
				continue;
			}

			if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
				$post_id = apply_filters( 'wpml_object_id', $post_id, 'page', true );
			}

			$post = get_post( $post_id );
			if ( ! empty( $post ) && ! is_array( $post ) ) {

				$main_page_url = get_permalink( $post_id );
				if ( ! empty( $main_page_url ) && ! is_wp_error( $main_page_url ) ) {

					$main_page_path = urldecode( sanitize_title_with_dashes( $post->post_name, '', 'save' ) );
					if ( $main_page_path == $kb_config['kb_articles_common_path'] ) {
						return $main_page_url;
					}
					$first_page_url = empty( $first_page_url ) ?  $main_page_url : $first_page_url;
				}
			}
		}

		return $first_page_url;
	}

	/**
	 * Find KB Main Page ID (page that matches kb_articles_common_path in KB config or first main page id).
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_first_kb_main_page_id( $kb_config ) {

		$first_post_id = '';
		$kb_main_pages = $kb_config['kb_main_pages'];

		foreach ( $kb_main_pages as $post_id => $post_title ) {

			if ( count( $kb_main_pages ) == 1 ) {
				return $post_id;
			}

			$post = get_post( $post_id );
			if ( ! empty( $post ) && ! is_array( $post ) ) {

				$main_page_path = urldecode( sanitize_title_with_dashes( $post->post_name, '', 'save' ) );
				if ( $main_page_path == $kb_config['kb_articles_common_path'] ) {
					return $post_id;
				}
				$first_post_id = empty( $first_post_id ) ?  $post_id : $first_post_id;
			}
		}

		return $first_post_id;
	}

	/**
	 * Find first article url.
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_first_kb_article_url( $kb_config ) {

		$custom_categories_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( empty($custom_categories_data) ) {
			return '';
		}

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		if ( empty( $articles_seq_data ) ) {
			return '';
		}

		$category_seq_array = EPKB_Articles_Setup::epkb_get_array_keys_multiarray( $custom_categories_data, $kb_config );
		foreach( $category_seq_array as $cat_seq_id ) {

			if ( empty( $articles_seq_data[$cat_seq_id] ) || sizeof( $articles_seq_data[$cat_seq_id] ) < 3 ) {
				continue;
			}

			$data = array_keys( $articles_seq_data[$cat_seq_id] );

			for( $i = 2; $i < count( $data ); $i++ ) {

				if ( empty( $data[$i] ) ) {
					continue;
				}

				$article_id = $data[$i];
				if ( ! EPKB_Utilities::is_link_editor_enabled() ) {
					return get_permalink( $article_id );
				}

				$post = get_post( $article_id );

				if ( empty( $post ) ) {
					continue;
				}

				// check if KB Article is linked
				if ( EPKB_Utilities::is_link_editor( $post ) ) {
					continue;
				}

				return get_permalink( $article_id );
			}

		}

		return '';
	}

	/**
	 * Find category with most articles
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_kb_category_with_most_articles_url( $kb_config ) {

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		if ( empty($articles_seq_data) ) {
			return '';
		}

		$articles_seq_data_length = array_map('sizeof', $articles_seq_data);
		arsort($articles_seq_data_length);
		$category_id = key($articles_seq_data_length); // the top is the category with most articles

		if ( empty($category_id) ) {
			return '';
		}

		return get_category_link( $category_id );
	}

	/**
	 * Get relevant KB id
	 *
	 * @return int
	 */
	public static function get_relevant_kb_id() {

		// define relevant KB id
		$kb_id = self::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		return (int)$kb_id;
	}

	/**
	 * Regenerate KB sequence for Categories and Articles if missing
	 *
	 * @param int $kb_id
	 * @param array $category_seq_data
	 *
	 * @return array|string|null
	 */
	public static function get_refreshed_kb_categories( $kb_id, $category_seq_data=null ) {

		if ( $category_seq_data === null ) {
			$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		}

		// if non-empty categories sequence in DB then nothing to do
		if ( ! empty( $category_seq_data ) && is_array( $category_seq_data ) ) {
			return $category_seq_data;
		}

		// determine why categories are missing - get categories from DB
		$all_terms = EPKB_Core_Utilities::get_kb_categories_visible( $kb_id );
		// if error then nothing to do
		if ( $all_terms === null ) {
			return null;
		}
		// if empty then that is expected
		if ( empty( $all_terms ) ) {
			return [];
		}

		// regenerate articles
		$article_admin = new EPKB_Articles_Admin();
		$is_articles_updated = $article_admin->update_articles_sequence( $kb_id ); // ignore result as we are focusing on categories
		if ( ! $is_articles_updated ) {
			EPKB_Logging::add_log( 'Could not update article sequence for KB ' . $kb_id );
		}

		// regenerate categories
		$category_admin = new EPKB_Categories_Admin();
		$category_taxonomy_slug = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$result = $category_admin->update_categories_sequence( 0, 0, $category_taxonomy_slug );
		if ( empty( $result ) ) {
			EPKB_Logging::add_log( 'Could not update category sequence for KB ' . $kb_id );
			return null;
		}

		return EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
	}
}
