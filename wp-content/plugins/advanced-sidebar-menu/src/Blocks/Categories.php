<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;

/**
 * Advanced Sidebar - Categories, Gutenberg block.
 *
 * @since  9.0.0
 */
class Categories extends Block_Abstract {
	use Singleton;

	const NAME = 'advanced-sidebar-menu/categories';


	/**
	 * Get the description of this block.
	 *
	 * @return string
	 */
	protected function get_description() {
		return __( 'Creates a menu of all the categories using the parent/child relationship',
			'advanced-sidebar-menu' );
	}


	/**
	 * Get featured this block supports.
	 *
	 * Done on the PHP side, so we can easily add additional features
	 * via the PRO version.
	 *
	 * @return array
	 */
	protected function get_block_support() {
		return apply_filters( 'advanced-sidebar-menu/blocks/categories/supports', [
			'anchor' => true,
		] );
	}


	/**
	 * Get list of words used to search for the block.
	 *
	 * English and translated so both will be searchable.
	 *
	 * @return array
	 */
	public function get_keywords() {
		$category = get_taxonomy( 'category' );

		return [
			'Advanced Sidebar',
			'menu',
			'sidebar',
			'category',
			'categories',
			'taxonomy',
			'term',
			$category ? $category->labels->name : '',
			$category ? $category->labels->singular_name : '',
			__( 'menu', 'advanced-sidebar-menu' ),
			__( 'sidebar', 'advanced-sidebar-menu' ),
			__( 'taxonomy', 'advanced-sidebar-menu' ),
			__( 'term', 'advanced-sidebar-menu' ),
		];
	}


	/**
	 * Get list of attributes and their types.
	 *
	 * Must be done PHP side because we're using ServerSideRender
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/
	 *
	 * @return array
	 */
	protected function get_attributes() {
		return apply_filters( 'advanced-sidebar-menu/blocks/categories/attributes', [
			Category::INCLUDE_PARENT           => [
				'type' => 'boolean',
			],
			Category::INCLUDE_CHILDLESS_PARENT => [
				'type' => 'boolean',
			],
			Category::EXCLUDE               => [
				'type' => 'string',
			],
			Category::DISPLAY_ALL           => [
				'type' => 'boolean',
			],
			Category::DISPLAY_ON_SINGLE     => [
				'type'    => 'boolean',
				'default' => true,
			],
			// No block option available. We only support 'list'.
			Category::EACH_CATEGORY_DISPLAY => [
				'type'    => 'string',
				'default' => \Advanced_Sidebar_Menu\Menus\Category::EACH_LIST,
			],
			Category::LEVELS                => [
				'type'    => 'number',
				'default' => 100,
			],
		] );
	}


	/**
	 * Return a new instance of the Page widget.
	 *
	 * @return Category
	 */
	protected function get_widget_class() {
		return new Category();
	}

}
