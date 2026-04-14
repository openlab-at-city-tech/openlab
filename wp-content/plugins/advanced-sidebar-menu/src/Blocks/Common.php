<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Blocks\Register\Attribute;
use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Attributes and other configuration shared by all blocks.
 *
 * Done in a common way to make the passed JS CONFIG as small as possible.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 */
class Common {
	use Singleton;

	/**
	 * Get all attributes shared by all blocks.
	 *
	 * @phpstan-return array<'style'|'title', Attribute>
	 * @return array
	 */
	public function get_common_attributes(): array {
		return (array) apply_filters( 'advanced-sidebar-menu/blocks/common-attributes/attributes', [
			'style'              => Attribute::factory( [
				'type' => 'object',
			] ),
			Menu_Abstract::TITLE => Attribute::factory( [
				'type' => 'string',
			] ),
		], $this );
	}


	/**
	 * Common features all blocks supports.
	 *
	 * @link   https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports
	 *
	 * @return array<string, mixed>
	 */
	public function get_block_supports(): array {
		$basic_support = [
			'anchor' => true,
			'html'   => false,
		];

		return (array) apply_filters( 'advanced-sidebar-menu/blocks/common-attributes/supports', $basic_support, $this );
	}


	/**
	 * Get all attributes used for previewing the block.
	 *
	 * @phpstan-return array<'clientId'|'isServerSideRenderRequest'|'sidebarId', Attribute>
	 * @return array
	 */
	public function get_server_side_render_attributes(): array {
		return [
			Block_Abstract::BLOCK_ID       => Attribute::factory( [
				'type' => 'string',
			] ),
			Block_Abstract::RENDER_REQUEST => Attribute::factory( [
				'type' => 'boolean',
			] ),
			Block_Abstract::SIDEBAR_ID     => Attribute::factory( [
				'type' => 'string',
			] ),
		];
	}
}
