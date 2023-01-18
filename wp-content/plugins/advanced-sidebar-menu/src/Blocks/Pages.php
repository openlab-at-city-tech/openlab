<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Page;

/**
 * Advanced Sidebar - Pages, Gutenberg block.
 *
 * @since  9.0.0
 */
class Pages extends Block_Abstract {
	use Singleton;

	const NAME = 'advanced-sidebar-menu/pages';


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
		return apply_filters( 'advanced-sidebar-menu/blocks/pages/supports', [
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
		return [
			'Advanced Sidebar',
			'menu',
			'sidebar',
			'pages',
			__( 'menu', 'advanced-sidebar-menu' ),
			__( 'sidebar', 'advanced-sidebar-menu' ),
			__( 'pages', 'advanced-sidebar-menu' ),
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
		return apply_filters( 'advanced-sidebar-menu/blocks/pages/attributes', [
			Page::INCLUDE_PARENT           => [
				'type' => 'boolean',
			],
			Page::INCLUDE_CHILDLESS_PARENT => [
				'type' => 'boolean',
			],
			Page::ORDER_BY                 => [
				'type'    => 'string',
				'default' => 'menu_order',
			],
			Page::EXCLUDE                  => [
				'type'    => 'string',
				'default' => '',
			],
			Page::DISPLAY_ALL              => [
				'type' => 'boolean',
			],
			Page::LEVELS                   => [
				'type'    => 'number',
				'default' => 100,
			],
		] );
	}


	/**
	 * Return a new instance of the Page widget.
	 *
	 * @return Page
	 */
	protected function get_widget_class() {
		return new Page();
	}

}
