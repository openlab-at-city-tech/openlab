<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Methods used for block related features
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Block_Utilities {

	/**
	 * Determine if the current page has KB layout blocks
	 * @param $the_post
	 * @return bool
	 */
	public static function current_post_has_kb_layout_blocks( $the_post = null ) {
		static $cache = array();

		if ( ! self::is_block_enabled() ) {
			return false;
		}

		// Retrieve the global post if $the_post is not provided
		$global_post = empty( $GLOBALS['post'] ) ? null : $GLOBALS['post'];
		$found_post = empty( $the_post ) ? $global_post : $the_post;

		// Validate the found post
		if ( empty( $found_post ) || ! isset( $found_post->post_content ) || empty( $found_post->ID ) ) {
			return false;
		}

		$post_id = $found_post->ID;

		// Check if the result is already cached
		if ( isset( $cache[ $post_id ] ) ) {
			return $cache[ $post_id ];
		}

		// Perform the computation to determine if the post has KB layout blocks
		//$has_layout_blocks = self::parse_block_attributes_from_post( $found_post, '-layout' ) !== false;
		$has_kb_blocks = preg_match( '/wp:echo-knowledge-base\//i', $found_post->post_content );

		// Store the result in the cache for future use
		$cache[ $post_id ] = $has_kb_blocks;

		return $has_kb_blocks;
	}

	/**
	 * Retrieve block attributes from post content; return false if the block was not found in the post content
	 * @param $post
	 * @param $block_name
	 * @return false|array
	 */
	public static function parse_block_attributes_from_post( $post, $block_name ) {

		if ( ! self::is_block_enabled() ) {
			return false;
		}

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

	public static function is_block_enabled() {
		return false;
	}
}
