<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Methods used for block related features
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Block_Utilities {

	/**
	 * Determine if the current page has any KB blocks
	 * @param bool $clear_cache
	 * @return bool
	 */
	public static function current_post_has_kb_blocks( $clear_cache = true ) {
		static $cache = [];

		// NOTE: any updates/fixes to be applied to Elegant Layouts and Advanced Search
		if ( $clear_cache ) {
			$cache = [];
		}

		// Get the current post
		$found_post = EPKB_Core_Utilities::get_current_post();
		if ( ! $found_post ) {
			return false;
		}
		$post_id = $found_post->ID;

		// Check if the result is already cached
		if ( isset( $cache[ $post_id ] ) ) {
			return $cache[ $post_id ];
		}

		// Perform the computation to determine if the post has KB blocks
		//$has_layout_blocks = self::parse_block_attributes_from_post( $found_post, '-layout' ) !== false;
		$has_kb_blocks = self::content_has_kb_block( $found_post->post_content );

		// Store the result in the cache for future use
		$cache[ $post_id ] = $has_kb_blocks;

		return $has_kb_blocks;
	}

	public static function kb_main_page_has_kb_blocks( $kb_config ) {

		$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config );
		$current_main_page = empty( $main_page_id ) ? null : get_post( $main_page_id );

		if ( empty( $current_main_page ) || empty( $current_main_page->post_content ) ) {
			return false;
		}

		return self::content_has_kb_block( $current_main_page->post_content );
	}

	/**
	 * Detect whether the given content contains any KB block
	 * @param $content
	 * @return false|int
	 */
	public static function content_has_kb_block( $content ) {
		return preg_match( '/wp:echo-knowledge-base\//i', $content );
	}

	/**
	 * Detect whether the given content contains KB block with the given name
	 * @param $content
	 * @param $block_name
	 * @return false|int
	 */
	public static function content_has_the_kb_block( $content, $block_name ) {
		return preg_match( '/wp:echo-knowledge-base\/' . preg_quote( $block_name, '/' ) . '/i', $content );
	}

	public static function get_kb_block_layout( $post, $default_layout = false ) {
		$blocks = self::parse_blocks( $post );
		if ( is_array( $blocks ) && count( $blocks ) ) {
			$layout = self::find_layout_block_recursive( $blocks );
			if ( $layout !== false ) {
				return $layout;
			}
		}
		return $default_layout;
	}

	/**
	 * Recursively search for a KB layout block and return the layout name
	 * @param array $blocks - Array of parsed blocks
	 * @return string|false - Layout name or false if not found
	 */
	private static function find_layout_block_recursive( $blocks ) {
		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) && strpos( $block['blockName'], 'echo-knowledge-base/' ) !== false && strpos( $block['blockName'], '-layout' ) !== false ) {
				// Extract layout name from block name (e.g., 'echo-knowledge-base/classic-layout' -> 'Classic')
				$block_slug = str_replace( array( 'echo-knowledge-base/', '-layout' ), '', $block['blockName'] );
				// Convert to layout name format: 'drill-down' -> 'Drill-Down', 'classic' -> 'Classic'
				return implode( '-', array_map( 'ucfirst', explode( '-', $block_slug ) ) );
			}
			// Search in inner blocks
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$result = self::find_layout_block_recursive( $block['innerBlocks'] );
				if ( $result !== false ) {
					return $result;
				}
			}
		}
		return false;
	}

	/**
	 * Retrieve block attributes from post content; return false if the block was not found in the post content
	 * @param $post
	 * @param $block_name
	 * @return false|array
	 */
	public static function parse_block_attributes_from_post( $post, $block_name ) {

		if ( empty( $post ) ) {
			return false;
		}

		$blocks = self::parse_blocks( $post );
		if ( is_array( $blocks ) && count( $blocks ) ) {
			return self::parse_block_attributes_recursive( $blocks, $block_name );
		}

		return false;
	}

	/**
	 * Find blocks in given post
	 * @param $post
	 * @return array|array[]
	 */
	private static function parse_blocks( $post ) {
		$blocks = [];

		if ( empty( $post->post_content ) ) {
			return $blocks;
		}

		if ( function_exists( 'parse_blocks' ) ) { // added  in wp 5.0
			$blocks = parse_blocks( $post->post_content );
		}

		return $blocks;
	}

	/**
	 * Retrieve block attributes from blocks; return false if the block was not found in the blocks
	 * @param $blocks
	 * @param $block_name
	 * @return false|array
	 */
	private static function parse_block_attributes_recursive( $blocks, $block_name ) {
		foreach ( $blocks as $block ) {

			// parse top level blocks
			if ( isset( $block['blockName'] ) ) {

				// match KB blocks by name
				if ( $block['blockName'] == EPKB_Abstract_Block::EPKB_BLOCK_NAMESPACE . '/' . $block_name ) {
					return isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : [];
				}

				// optionally detect any layout blocks - '-layout' indicates that we try to find any layout block
				if ( $block_name == '-layout' && strpos( $block['blockName'], EPKB_Abstract_Block::EPKB_BLOCK_NAMESPACE . '/' ) !== false && substr( $block['blockName'], -7 ) == $block_name ) {
					return isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : [];
				}
			}

			// parse inner blocks
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$result = self::parse_block_attributes_recursive( $block['innerBlocks'], $block_name );
				if ( $result !== false ) {
					return $result;
				}
			}
		}
		return false;
	}

	/**
	 * Return class handler of corresponding KB block depending on KB module name and KB Main Page layout; null if no corresponding block class found
	 * @param $module_name
	 * @param $layout
	 * @return EPKB_FAQs_Block|EPKB_Featured_Articles_Block|EPKB_Basic_Layout_Block|EPKB_Categories_Layout_Block|EPKB_Classic_Layout_Block|EPKB_Drill_Down_Layout_Block|EPKB_Search_Block|EPKB_Tabs_Layout_Block|EPKB_Grid_Layout_Block|EPKB_Sidebar_Layout_Block|EPKB_Advanced_Search_Block|null
	 *
	 */
	private static function get_block_class_by_module_name( $module_name, $layout ) {

		switch ( $module_name ) {

			case 'search':
				return new EPKB_Search_Block( false );

			case 'categories_articles':
				switch ( $layout ) {

					case EPKB_Layout::BASIC_LAYOUT:
						return new EPKB_Basic_Layout_Block( false );

					case EPKB_Layout::TABS_LAYOUT:
						return new EPKB_Tabs_Layout_Block( false );

					case EPKB_Layout::CLASSIC_LAYOUT:
						return new EPKB_Classic_Layout_Block( false );

					case EPKB_Layout::DRILL_DOWN_LAYOUT:
						return new EPKB_Drill_Down_Layout_Block( false );

					case EPKB_Layout::CATEGORIES_LAYOUT:
						return new EPKB_Categories_Layout_Block( false );

					case EPKB_Layout::GRID_LAYOUT:
						return new EPKB_Grid_Layout_Block( false );

					case EPKB_Layout::SIDEBAR_LAYOUT:
						return new EPKB_Sidebar_Layout_Block( false );

					default:
						return null;
				}

			case 'faqs':
				return new EPKB_FAQs_Block();

			case 'articles_list':
				return new EPKB_Featured_Articles_Block();

			case 'advanced-search':
				return new EPKB_Advanced_Search_Block( false );

			default:
				return null;
		}
	}

	/**
	 * Return array of block configurations depending on the given KB configuration (e.g. enabled modules and their settings)
	 * @param $kb_id
	 * @param $kb_config
	 * @return array
	 */
	public static function convert_blocks_config_from_kb_config( $kb_id, $kb_config ) {

		$kb_blocks = array();
		for ( $i = 1; $i <= 5; $i++ ) {

			$block_class_handler = self::get_block_class_by_module_name( $kb_config['ml_row_' . $i . '_module'], $kb_config['kb_main_page_layout'] );

			// do not continue if corresponding block class was not found for the current module
			if ( empty( $block_class_handler ) ) {
				continue;
			}

			$default_attributes = $block_class_handler->get_block_attributes_defaults();

			// block needs to store only attributes which have non-default value
			$non_default_attributes = array();
			foreach ( $default_attributes as $attribute_name => $attribute_value ) {

				// skip missing attribute names in KB configuration
				if ( ! isset( $kb_config[ $attribute_name ] ) ) {
					continue;
				}

				// skip the same values
				if ( $attribute_value == $kb_config[ $attribute_name ] ) {
					continue;
				}

				$non_default_attributes[ $attribute_name ] = $kb_config[ $attribute_name ];
			}

			// set required block-only attributes
			if ( $default_attributes['kb_id'] != $kb_id ) {
				$non_default_attributes['kb_id'] = $kb_id;
			}

			// if there are no non-default attributes, then the encoded string must be empty, otherwise it will lead to not rendered block
			$attributes_encoded = empty( $non_default_attributes ) ? '' : json_encode( $non_default_attributes );

			$kb_blocks[] = array(
				'name' => $block_class_handler::EPKB_BLOCK_NAME,
				'attributes' => $attributes_encoded,
			);
		}
		
		return $kb_blocks;
	}


	/************************************************************************
	 * 
	 * 	BLOCK UTILITIES (AI validated)
	 * 
	 ************************************************************************/

	/**
	 * Check whether given template is the KB block page template
	 * @param $template
	 * @return bool
	 */
	public static function is_kb_block_page_template( $template ) {
		return ! empty( $template->slug ) && $template->slug == EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE;
	}

	/**
	 * We allow KB block template only if WP version is 6.7 or higher and the current theme is a block theme;
	 * classic theme with block template can have issues.
	 * @return bool
	 */
	public static function is_kb_block_page_template_available() {
		global $wp_version;
		static $epkb_is_kb_block_page_template_available = null;

		if ( $epkb_is_kb_block_page_template_available === null ) {
			$epkb_is_kb_block_page_template_available = version_compare( $wp_version, '6.7', '>=' ) && self::is_block_theme();
		}

		return $epkb_is_kb_block_page_template_available;
	}

	/**
	 * Check if the current theme is a block theme.
	 * @return bool
	 */
	public static function is_block_theme() {
		static $is_block_theme = null;
	
		if ( $is_block_theme !== null ) {
			return $is_block_theme;
		}
	
		// Core helper – safest and most up to date.
		if ( function_exists( 'wp_is_block_theme' ) ) {
			$is_block_theme = (bool) wp_is_block_theme();
		}
		// Fallback ONLY if the core helper is unavailable.
		elseif ( function_exists( 'gutenberg_is_fse_theme' ) ) { // Gutenberg < 11.6  (WP pre-5.9)
			/**@disregard P1010 */
			$is_block_theme = (bool) gutenberg_is_fse_theme();
		}
		// Ultimate fallback.
		else {
			$is_block_theme = false;
		}
	
		return $is_block_theme;
	}

	/**
	 * Whether blocks are available in Guttenberg editor
	 * @return bool
	 */
	public static function is_blocks_available() {
		return EPKB_Block_Utilities::is_block_theme() || EPKB_Block_Utilities::current_theme_has_block_support();
	}

	/**
	 * Check if the current theme supports blocks whether it is classic theme or block theme
	 * @return bool
	 */
	public static function current_theme_has_block_support() {
		return use_block_editor_for_post_type( 'page' );
	}

	/**
	 * Update KB blocks on a page when layout or style changes via the Setup Wizard.
	 * @param int $page_id - The page ID containing KB blocks
	 * @param array $new_config - The new KB configuration after wizard changes
	 * @param string|null $new_layout - The new layout name (if layout changed)
	 * @param array $orig_config - The original KB configuration before changes
	 * @return bool|WP_Error - true on success, WP_Error on failure
	 */
	public static function update_kb_blocks_on_page( $page_id, $new_config, $new_layout = null, $orig_config = array() ) {

		$post = get_post( $page_id );
		if ( empty( $post ) || empty( $post->post_content ) ) {
			return new WP_Error( 'empty_post', 'Post not found or empty content' );
		}

		// Check if page has KB blocks
		if ( ! self::content_has_kb_block( $post->post_content ) ) {
			return new WP_Error( 'no_kb_blocks', 'Page does not contain KB blocks' );
		}

		$kb_id = $new_config['id'];
		$blocks = self::parse_blocks( $post );
		if ( empty( $blocks ) ) {
			return new WP_Error( 'parse_failed', 'Failed to parse blocks' );
		}

		// Determine old and new layout
		$old_layout = ! empty( $orig_config['kb_main_page_layout'] ) ? $orig_config['kb_main_page_layout'] : $new_config['kb_main_page_layout'];
		$target_layout = ! empty( $new_layout ) ? $new_layout : $new_config['kb_main_page_layout'];

		// Get block class for the new layout to retrieve default attributes
		$layout_block_class = self::get_block_class_by_module_name( 'categories_articles', $target_layout );
		if ( empty( $layout_block_class ) ) {
			return new WP_Error( 'unknown_layout', 'Unknown layout: ' . $target_layout );
		}

		$default_attributes = $layout_block_class->get_block_attributes_defaults();
		$new_block_name = EPKB_Abstract_Block::EPKB_BLOCK_NAMESPACE . '/' . $layout_block_class::EPKB_BLOCK_NAME;

		// Process blocks recursively
		$updated_blocks = self::update_blocks_recursive( $blocks, $kb_id, $new_config, $new_block_name, $default_attributes, $target_layout );

		// Serialize blocks back to content
		$new_content = '';
		foreach ( $updated_blocks as $block ) {
			$new_content .= serialize_block( $block );
		}

		// Update the post
		$result = wp_update_post( array(
			'ID' => $page_id,
			'post_content' => $new_content,
		), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Recursively update blocks, replacing layout blocks with new layout and updating attributes.
	 * @param array $blocks - Array of parsed blocks
	 * @param int $kb_id - The KB ID
	 * @param array $new_config - New KB configuration
	 * @param string $new_block_name - The new layout block name
	 * @param array $default_attributes - Default attributes for the new block
	 * @param string $target_layout - The target layout name
	 * @return array - Updated blocks
	 */
	private static function update_blocks_recursive( $blocks, $kb_id, $new_config, $new_block_name, $default_attributes, $target_layout ) {

		$layout_block_suffixes = array( '-layout', 'basic-layout', 'tabs-layout', 'categories-layout', 'classic-layout', 'drill-down-layout', 'grid-layout', 'sidebar-layout' );

		foreach ( $blocks as &$block ) {

			// Check if this is a KB layout block
			if ( ! empty( $block['blockName'] ) && strpos( $block['blockName'], EPKB_Abstract_Block::EPKB_BLOCK_NAMESPACE . '/' ) === 0 ) {

				$is_layout_block = false;
				foreach ( $layout_block_suffixes as $suffix ) {
					if ( strpos( $block['blockName'], $suffix ) !== false ) {
						$is_layout_block = true;
						break;
					}
				}

				// Update layout block
				if ( $is_layout_block ) {
					// Change block name to new layout
					$block['blockName'] = $new_block_name;

					// Build non-default attributes for the new block while keeping block-only settings intact
					$block_attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
					foreach ( $default_attributes as $attr_name => $default_value ) {
						if ( ! array_key_exists( $attr_name, $new_config ) ) {
							continue;
						}

						// Remove attribute if it matches default value since we only want to store non-default attributes for blocks
						if ( $new_config[ $attr_name ] == $default_value ) {
							unset( $block_attrs[ $attr_name ] );
							continue;
						}

						$block_attrs[ $attr_name ] = $new_config[ $attr_name ];
					}

					// Always include kb_id if different from default
					if ( $default_attributes['kb_id'] != $kb_id ) {
						$block_attrs['kb_id'] = $kb_id;
					}

					$block['attrs'] = $block_attrs;
				}
			}

			// Process inner blocks recursively
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$block['innerBlocks'] = self::update_blocks_recursive( $block['innerBlocks'], $kb_id, $new_config, $new_block_name, $default_attributes, $target_layout );
			}
		}

		return $blocks;
	}
}
