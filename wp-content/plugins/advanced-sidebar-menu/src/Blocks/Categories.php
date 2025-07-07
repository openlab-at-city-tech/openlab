<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Blocks\Attributes\CategoryAttr;
use Advanced_Sidebar_Menu\Blocks\Register\Attribute;
use Advanced_Sidebar_Menu\Menus\Category as Menu;
use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category as Widget;

/**
 * Advanced Sidebar - Categories, Gutenberg block.
 *
 * @since  9.0.0
 *
 * @phpstan-import-type ATTR_SHAPE from Block_Abstract
 * @phpstan-import-type CATEGORY_SETTINGS from Widget as WIDGET_SETTINGS
 * @phpstan-import-type DEFAULTS from Widget
 *
 * @phpstan-type CATEGORY_ATTRIBUTES array{
 *     display_all: bool,
 *     exclude: string,
 *     include_childless_parent: bool,
 *     include_parent: bool,
 *     levels: int,
 *     new_widget: 'list'|'widget',
 *     single: bool,
 *     taxonomy?: string,
 * }
 * @extends Block_Abstract<CATEGORY_ATTRIBUTES, WIDGET_SETTINGS, DEFAULTS>
 * @implements Block<WIDGET_SETTINGS, DEFAULTS>
 */
class Categories extends Block_Abstract implements Block {
	use Singleton;

	public const NAME = 'advanced-sidebar-menu/categories';


	/**
	 * Get the name of this block.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return self::NAME;
	}


	/**
	 * Get the description of this block.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __( 'Creates a menu of all the categories using the parent/child relationship',
			'advanced-sidebar-menu' );
	}


	/**
	 * Return a new instance of the Categories widget.
	 *
	 * @return Widget
	 */
	public function get_widget_class(): Widget {
		return new Widget();
	}


	/**
	 * Get the list of attributes and their types.
	 *
	 * Must be done on both PHP and JS sides to support default values
	 * * and SeverSideRender.
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/
	 *
	 * @phpstan-return array<key-of<CATEGORY_ATTRIBUTES>, ATTR_SHAPE>
	 * @return array
	 */
	public function get_attributes() {
		return apply_filters( 'advanced-sidebar-menu/blocks/categories/attributes', [
			Widget::INCLUDE_PARENT           => Attribute::factory( [
				'type'    => 'boolean',
				'default' => false,
			] ),
			Widget::INCLUDE_CHILDLESS_PARENT => Attribute::factory( [
				'type'    => 'boolean',
				'default' => false,
			] ),
			Widget::EXCLUDE                  => Attribute::factory( [
				'type'    => 'string',
				'default' => '',
			] ),
			Widget::DISPLAY_ALL              => Attribute::factory( [
				'type'    => 'boolean',
				'default' => false,
			] ),
			Widget::DISPLAY_ON_SINGLE        => Attribute::factory( [
				'type'    => 'boolean',
				'default' => true,
			] ),
			// No block option available. We only support 'list'.
			Widget::POST_CATEGORY_LAYOUT     => Attribute::factory( [
				'type'    => 'string',
				'default' => Menu::EACH_LIST,
				'enum'    => [
					Menu::EACH_LIST,
					Menu::EACH_WIDGET,
				],
			] ),
			Widget::LEVELS                   => Attribute::factory( [
				'type'    => 'number',
				'default' => 100,
			] ),
		] );
	}


	/**
	 * @deprecated 9.7.0
	 *
	 * @phpstan-return array<string, bool>
	 */
	protected function get_block_support() {
		_deprecated_function( __METHOD__, '9.7.0' );

		return apply_filters( 'advanced-sidebar-menu/blocks/categories/supports', [
			'anchor' => true,
			'html'   => false,
		] );
	}


	/**
	 * @deprecated 9.7.0
	 *
	 * @phpstan-return array<string>
	 */
	public function get_keywords() {
		_deprecated_function( __METHOD__, '9.7.0' );

		$category = get_taxonomy( 'category' );

		return [
			'Advanced Sidebar',
			'menu',
			'sidebar',
			'category',
			'categories',
			'taxonomy',
			'term',
			false !== $category ? $category->labels->name : '',
			false !== $category ? $category->labels->singular_name : '',
			__( 'menu', 'advanced-sidebar-menu' ),
			__( 'sidebar', 'advanced-sidebar-menu' ),
			__( 'taxonomy', 'advanced-sidebar-menu' ),
			__( 'term', 'advanced-sidebar-menu' ),
		];
	}
}
