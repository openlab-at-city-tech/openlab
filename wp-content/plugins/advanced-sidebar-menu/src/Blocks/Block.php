<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Blocks\Register\Attribute;
use Advanced_Sidebar_Menu\Widget\Widget;

/**
 * Rules a block must follow.
 *
 * Replacement for `Block_Abstract` as we move from inheritance to composition.
 *
 * @note   Do not change this interface without bumping a major version.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 *
 * @phpstan-import-type ATTR_SHAPE from Block_Abstract
 *
 * @template SETTINGS of array<string, string|int|bool|array<string, string>>
 * @template DEFAULTS of array<key-of<SETTINGS>, int|string|array<string, string>>
 */
interface Block {
	/**
	 * Get the name of this block.
	 *
	 * @return string
	 */
	public function get_name(): string;


	/**
	 * Get the list of attributes and their types.
	 *
	 * Must be done on both PHP and JS sides to support default values
	 * and SeverSideRender.
	 *
	 * @see  Pro_Block_Abstract::get_all_attributes()
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/
	 *
	 * @return array<string, ATTR_SHAPE|Attribute>
	 */
	public function get_attributes();


	/**
	 * Get the widget class, which matches this block.
	 *
	 * @return Widget<SETTINGS, DEFAULTS>
	 */
	public function get_widget_class(): Widget;


	/**
	 * Get the description of this block.
	 *
	 * @return string
	 */
	public function get_description(): string;
}
