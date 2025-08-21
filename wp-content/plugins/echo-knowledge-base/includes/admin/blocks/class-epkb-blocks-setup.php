<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EPKB_Blocks_Setup {

	public function __construct() {
		global $pagenow;

		// do not KB blocks in widgets area
		if ( ! empty( $pagenow ) && $pagenow == 'widgets.php' ) {
			return;
		}

		// show search blocks first
		if ( EPKB_Utilities::is_advanced_search_enabled() ) {
			new EPKB_Advanced_Search_Block();
		}
		new EPKB_Search_Block();

		// show pro blocks next
		$enable_elay_blocks = EPKB_Utilities::is_elegant_layouts_enabled() && class_exists( 'Echo_Elegant_Layouts' ) && version_compare( Echo_Elegant_Layouts::$version, '3.0.0', '>=' );
		if ( $enable_elay_blocks ) {
			new EPKB_Grid_Layout_Block();
			new EPKB_Sidebar_Layout_Block();
		}

		// core blocks
		new EPKB_Basic_Layout_Block();
		new EPKB_Tabs_Layout_Block();
		new EPKB_Categories_Layout_Block();
		new EPKB_Classic_Layout_Block();
		new EPKB_Drill_Down_Layout_Block();
		new EPKB_FAQs_Block();
		new EPKB_Featured_Articles_Block();

		add_action( 'init', array( $this, 'initialize' ) );
		add_filter( 'get_block_templates', array( $this, 'reassign_kb_block_page_template_with_numerical_key' ), 1, 3 );
	}

	public function initialize() {
		$this->register_block_category();
		$this->add_kb_block_page_template_if_missing();
	}

	protected function register_block_category() {
		// block_categories_all is a replacement for block_categories filter from WP v5.8
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) && class_exists( 'WP_Block_Editor_Context' ) ) {
			add_filter( 'block_categories_all', array( $this, 'add_block_category' ), 10, 2 );
		} else {
			add_filter( 'block_categories', array( $this, 'add_block_category' ), 10, 2 );
		}
	}

	/**
	 * Register product related block categories.
	 *
	 * @param array[] $block_categories Array of categories for block types.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 */
	public function add_block_category( $block_categories, $block_editor_context ) {

		// ensure the category is added only once (in case of child classes)
		if ( in_array( EPKB_Abstract_Block::EPKB_BLOCK_CATEGORY, array_column( $block_categories, 'slug' ) ) ) {
			return $block_categories;
		}

		array_unshift( $block_categories,
			array(
				'slug' => EPKB_Abstract_Block::EPKB_BLOCK_CATEGORY,
				'title' => esc_html__( 'Echo Knowledge Base', 'echo-knowledge-base' ),
				'icon' => EPKB_Abstract_Block::EPKB_BLOCK_CATEGORY_ICON,
			)
		);

		return $block_categories;
	}

	/**
	 *  For KB block Main Page we provide custom page blocks template
	 * @return void
	 */
	private function add_kb_block_page_template_if_missing() {

		// new classes are available only in WP 6.7 and later
		if ( ! class_exists( 'WP_Block_Templates_Registry' ) || ! function_exists( 'register_block_template' ) ) {
			return;
		}

		// non-block Themes does not work correctly with fully block templates - particularly set up of the page template and save functionality in block editor
		if ( ! EPKB_Block_Utilities::is_kb_block_page_template_available() ) {
			return;
		}

		$template_name = EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_NAMESPACE . '//' . EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE;

		// skip registration if the template was already registered.
		if ( WP_Block_Templates_Registry::get_instance()->is_registered( $template_name ) ) {
			return;
		}

		// define the content of the template
		$template_content =
			'<!-- wp:template-part {"slug":"header"} /-->
			<!-- wp:post-content {"lock":{"remove":true}} /-->
			<!-- wp:template-part {"slug":"footer"} /-->';

		// register template
		register_block_template( $template_name, [
			'title' => esc_html__( 'KB Block Page Template', 'echo-knowledge-base' ),
			'description' => esc_html__( 'The recommended template to use for creating the KB main page.', 'echo-knowledge-base' ),
			'content' => $template_content,
			'post_types'  => array( 'page' ),
		] );
	}

	/**
	 * This filter is need to remove undefined offset (PHP notice) on the block Editor page - this is a quick fix for the core WordPress issue
	 * TODO: the issue is reported to WordPress support and the filter will be not required when the issue will be fixed
	 * 		(unless we want to ensure users with not updated WordPress have the template working correctly)
	 * 		ticket: https://core.trac.wordpress.org/ticket/62407
	 * @param $block_templates
	 * @param $query
	 * @param $template_type
	 * @return mixed
	 */
	public function reassign_kb_block_page_template_with_numerical_key( $block_templates, $query, $template_type ) {

		// do nothing if there is no KB block page template id as a key in the templates array
		$kb_block_page_template_id = EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_NAMESPACE . '//' . EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE;
		if ( empty( $block_templates[ $kb_block_page_template_id ] ) ) {
			return $block_templates;
		}

		foreach ( $block_templates as $key => $template ) {
			if ( $key === $kb_block_page_template_id ) {
				$block_templates[] = $template;
				unset( $block_templates[ $key ] );
			}
		}

		return $block_templates;
	}

	/**
	 * NOTE: this method to register template has issues on switching Themes - it makes newly activated Theme incorrectly parsing KB template which was previously edited by previous Theme
	 * For KB block Main Page we provide custom page template - ensure the template already exists and is assigned for the current Theme.
	 * @return void
	 */
	private static function add_kb_block_page_template_if_missing__deprecated() {

		// get current Theme slug
		$theme = wp_get_theme();
		$current_theme_slug = $theme->get_stylesheet();

		// check if the template already exists for the current theme
		$kb_block_page_template_id = $current_theme_slug . '//' . EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE;
		$existing_kb_block_template = get_block_template( $kb_block_page_template_id );

		// CASE: error returned on the template retrieval - do not continue as it is better to not have the template added rather than create duplicates
		if ( is_wp_error( $existing_kb_block_template ) ) {
			EPKB_Logging::add_log( 'Failed to retrieve KB block page template.', $existing_kb_block_template );
			return;
		}

		// CASE: the template already registered for the current Theme - do nothing
		if ( $existing_kb_block_template ) {
			return;
		}

		// try to retrieve existing template - the KB block page template may exist here but is not associated with the current Theme
		$existing_kb_block_templates = get_posts( array(
			'post_type'      => 'wp_template',
			'name'           => EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		) );


		// CASE: template is missing - create the template
		if ( empty( $existing_kb_block_templates ) ) {

			// define the content of the template
			$template_content =
				'<!-- wp:template-part {"slug":"header"} /-->
				<!-- wp:post-content {"lock":{"remove":true}} /-->
				<!-- wp:template-part {"slug":"footer"} /-->';

			// insert the custom template
			$inserted_template_id = wp_insert_post( array(
				'post_title'   => esc_html__( 'KB Block Page Template', 'echo-knowledge-base' ),
				'post_excerpt' => esc_html__( 'The recommended template to use for creating the KB main page.', 'echo-knowledge-base' ),
				'post_name'    => sanitize_title( EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE ),
				'post_content' => $template_content,
				'post_status'  => 'publish',
				'post_type'    => 'wp_template',
				'tax_input'    => array(
					'wp_theme' => array( $current_theme_slug ),
				),
				'meta_input' => array(
					'wp_template_type'	=> 'custom',
					'postTypes'			=> array( 'page' ),
				),
			), true );

			if ( is_wp_error( $inserted_template_id ) ) {
				EPKB_Logging::add_log( 'Failed to insert KB block page template.', $inserted_template_id );
			}

			return;
		}

		// CASE: template exists - check if the template is associated with the current theme
		$kb_block_page_template = reset( $existing_kb_block_templates );
		$terms = wp_get_object_terms( $kb_block_page_template->ID, 'wp_theme', array( 'fields' => 'slugs' ) );
		if ( ! in_array( $current_theme_slug, $terms, true ) ) {
			wp_set_object_terms( $kb_block_page_template->ID, array_merge( $terms, array( $current_theme_slug ) ), 'wp_theme' );
		}
	}

	/**
	 * NOTE: is needed for using add_kb_block_page_template_if_missing__deprecated() to filter out the template for non-page post type (use this filter for get_block_templates)
	 * Filter out KB block page template for non-page post type
	 * @param $block_templates
	 * @param $query
	 * @param $template_type
	 * @return mixed
	 */
	public static function filter_block_templates_by_post_type_deprecated( $block_templates, $query, $template_type ) {

		// only filter out the KB block page template if the post type is defined and is not page
		if ( empty( $query['post_type'] ) || $query['post_type'] == 'page' ) {
			return $block_templates;
		}

		// filter out KB block page template for non-page post type
		foreach ( $block_templates as $key => $template ) {
			if ( isset( $template->slug ) && $template->slug === EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE ) {
				unset( $block_templates[ $key ] );
			}
		}

		return $block_templates;
	}
}