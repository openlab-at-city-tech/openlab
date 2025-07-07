<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Notice;
use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Attributes and other configuration shared by all blocks.
 *
 * Done in a common way to make the passed JS CONFIG as small as possible.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 *
 * @phpstan-import-type ATTR_SHAPE from Block_Abstract
 */
class Common {
	use Singleton;

	/**
	 * Get all attributes shared by all blocks.
	 *
	 * @phpstan-return array<'style'|'title', ATTR_SHAPE>
	 * @return array
	 */
	public function get_common_attributes(): array {
		return (array) apply_filters( 'advanced-sidebar-menu/blocks/common-attributes/attributes', [
			'style'              => [
				'type' => 'object',
			],
			Menu_Abstract::TITLE => [
				'type' => 'string',
			],
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

		$filtered = (array) apply_filters( 'advanced-sidebar-menu/blocks/common-attributes/supports', $basic_support, $this );

		if ( ! $this->_temp_pro_supports_common() && null !== Notice::instance()->get_pro_version() ) {
			// Temporary shim to bring in common supports for all blocks for PRO < 9.9.0.
			// @todo Remove this filter once the required PRO version is 9.9.0+.
			return (array) apply_filters( 'advanced-sidebar-menu/blocks/pages/supports', $basic_support, $this );
		}
		return $filtered;
	}


	/**
	 * Get all attributes used for previewing the block.
	 *
	 * @phpstan-return array<'clientId'|'isServerSideRenderRequest'|'sidebarId', ATTR_SHAPE>
	 * @return array
	 */
	public function get_server_side_render_attributes(): array {
		return [
			Block_Abstract::BLOCK_ID       => [
				'type' => 'string',
			],
			Block_Abstract::RENDER_REQUEST => [
				'type' => 'boolean',
			],
			Block_Abstract::SIDEBAR_ID     => [
				'type' => 'string',
			],
		];
	}


	/**
	 * Check if the basic version supports common attributes.
	 *
	 * @todo Remove once the minimum PRO version is 9.9.0.
	 *
	 * @internal
	 *
	 * @return bool
	 */
	public function _temp_pro_supports_common(): bool { //phpcs:ignore
		// @phpstan-ignore function.impossibleType
		$supported = \class_exists( Pro_Common::class ) && \method_exists( Pro_Common::class, 'get_common_attributes' );
		return apply_filters_deprecated( 'advanced-sidebar-menu/blocks/common-attributes/pro-supports-common', [ $supported, $this ], '9.7.0' );
	}
}
