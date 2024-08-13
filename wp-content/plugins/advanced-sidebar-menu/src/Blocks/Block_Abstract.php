<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Scripts;
use Advanced_Sidebar_Menu\Utils;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;
use Advanced_Sidebar_Menu\Widget\Widget_Abstract;
use Advanced_Sidebar_Menu\Widget_Options\Shared\Style_Targeting;

/**
 * Functionality shared by and required by all blocks.
 *
 * @since 9.0.0
 *
 * @phpstan-import-type PAGE_SETTINGS from Page
 * @phpstan-import-type CATEGORY_SETTINGS from Category
 * @phpstan-import-type WIDGET_ARGS from Widget_Abstract
 *
 * @phpstan-type ATTR_SHAPE array{
 *    type: string,
 *    default?: mixed,
 *    enum?: array<string|int|bool>,
 *    items?: array{
 *      type: string,
 *      required?: array<string>,
 *      properties?: array<string, array{
 *         type: string,
 *      }>
 *    }
 * }
 *
 * @phpstan-type SHARED array{
 *     clientId?: string,
 *     sidebarId?: string,
 *     style?: array<string, string>,
 *     title?: string,
 *     isServerSideRenderRequest?: bool,
 * }
 *
 * @phpstan-template SETTINGS of array<string, string|int|bool|array<string, string>>
 */
abstract class Block_Abstract {
	public const NAME = 'block-abstract';

	public const RENDER_REQUEST = 'isServerSideRenderRequest';

	/**
	 * Widget arguments used in rendering.
	 *
	 * 1. Values are passed down through filters if used in a widget area.
	 * 2. We append the block wrap to `before_widget` before use to include
	 *  styles and other HTML attributes in the output.
	 *
	 * @phpstan-var WIDGET_ARGS
	 * @var array<string, string>
	 */
	protected $widget_args = [
		'before_widget' => '',
		'after_widget'  => '',
		// Default used for FSE.
		'before_title'  => '<h2 class="advanced-sidebar-menu-title">',
		'after_title'   => '</h2>',
	];


	/**
	 * Get list of attributes and their types.
	 *
	 * Must be done PHP side because we're using ServerSideRender.
	 *
	 * @see  Pro_Block_Abstract::get_all_attributes()
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/
	 *
	 * @return array
	 */
	abstract protected function get_attributes();


	/**
	 * Get featured this block supports.
	 *
	 * Done on the PHP side, so we can easily add additional features
	 * via the PRO version.
	 *
	 * @return array
	 */
	abstract protected function get_block_support();


	/**
	 * Get list of words used to search for the block.
	 *
	 * @return string[]
	 */
	abstract protected function get_keywords();


	/**
	 * Get the description of this block.
	 *
	 * @return string
	 */
	abstract protected function get_description();


	/**
	 * Get the widget class, which matches this block.
	 */
	abstract protected function get_widget_class();


	/**
	 * Actions and filters.
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'init', [ $this, 'register' ] );
		add_filter( 'advanced-sidebar-menu/scripts/js-config', [ $this, 'js_config' ] );
		add_filter( 'widget_display_callback', [ $this, 'short_circuit_widget_blocks' ], 10, 3 );
		add_filter( 'widget_types_to_hide_from_legacy_widget_block', [ $this, 'exclude_from_legacy_widgets' ] );
		add_filter( 'jetpack_widget_visibility_server_side_render_blocks', [ $this, 'add_jetpack_support' ] );
	}


	/**
	 * Exclude this block from new Legacy Widgets.
	 *
	 * Leave existing intact while forcing users to use the block
	 * instead for new widgets.
	 *
	 * @param array|bool $blocks - Excluded blocks.
	 *
	 * @return array|bool
	 */
	public function exclude_from_legacy_widgets( $blocks ) {
		if ( ! \is_array( $blocks ) ) {
			return $blocks;
		}

		/**
		 * Programmatically opt in to legacy widgets from the Block Inserter
		 * if legacy widgets are needed to match a theme's styles.
		 *
		 * @link https://developer.wordpress.org/block-editor/how-to-guides/widgets/legacy-widget-block/#3-hide-the-widget-from-the-legacy-widget-block
		 */
		if ( ! apply_filters( 'advanced-sidebar-menu/block-abstract/exclude-legacy-widgets', true, $this->get_widget_class(), $blocks, $this ) ) {
			return $blocks;
		}

		$widget = $this->get_widget_class();
		$blocks[] = $widget::NAME;
		return $blocks;
	}


	/**
	 * Store the widget arguments, so we send the appropriate wraps to the
	 * render of the menu.
	 *
	 * Using a block in widgets will wrap the contents of block
	 * in a `<section>` tag, regardless of the content within.
	 * We mimic the functionality of the inner echo while excluding
	 * the calls to output the wrap.
	 *
	 * @phpstan-param WIDGET_ARGS $args
	 *
	 * @param false|array $instance - Contents of the block, before parsing.
	 * @param \WP_Widget<PAGE_SETTINGS|CATEGORY_SETTINGS> $widget - Object representing a block based widget.
	 * @param array       $args     - Widget area arguments.
	 *
	 * @return false|array
	 */
	public function short_circuit_widget_blocks( $instance, $widget, array $args ) {
		if ( ! \is_array( $instance ) || ! isset( $instance['content'] ) || false === \strpos( $instance['content'], static::NAME ) ) {
			return $instance;
		}

		// Pass on the widget arguments.
		$this->widget_args = $args;

		echo apply_filters( 'widget_block_content', $instance['content'], $instance, $widget, $args ); //phpcs:ignore

		return false;
	}


	/**
	 * Jetpack assumes that no blocks will be using `ServerSideRender` and adds
	 * a custom `conditions` attribute to all blocks via JS. This causes the
	 * previews to fail due to invalid attributes.
	 *
	 * Only affected when Jetpack's "widget-visibility" module is active.
	 *
	 * By including the block name in the list of blocks that use `ServerSideRender`
	 * Jetpack will add the `conditions` attribute to the block.
	 *
	 * @ticket #11837
	 * @link https://github.com/Automattic/jetpack/pull/31928
	 *
	 * @param ?array $blocks Blocks that Jetpack supports.
	 *
	 * @return array
	 */
	public function add_jetpack_support( $blocks ): array {
		if ( ! \is_array( $blocks ) ) {
			$blocks = [];
		}
		$blocks[] = static::NAME;
		return $blocks;
	}


	/**
	 * Register the block.
	 *
	 * @link   https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/
	 *
	 * @see    Pro_Block_Abstract::register()
	 *
	 * @action init 10 0
	 *
	 * @return void
	 */
	public function register(): void {
		$args = apply_filters( 'advanced-sidebar-menu/block-register/' . static::NAME, [
			'api_version'           => 3,
			'attributes'            => $this->get_all_attributes(),
			'description'           => $this->get_description(),
			'editor_script_handles' => [ Scripts::GUTENBERG_HANDLE ],
			'editor_style_handles'  => [ Scripts::GUTENBERG_CSS_HANDLE ],
			'keywords'              => $this->get_keywords(),
			'render_callback'       => [ $this, 'render' ],
			'supports'              => $this->get_block_support(),
		] );

		// Translate deprecated keys until required PRO version is 9.5.0.
		if ( isset( $args['editor_script'] ) ) {
			$args['editor_script_handles'] = (array) $args['editor_script'];
			unset( $args['editor_script'] );
		}
		if ( isset( $args['editor_style'] ) ) {
			$args['editor_style_handles'] = (array) $args['editor_style'];
			unset( $args['editor_style'] );
		}

		register_block_type( static::NAME, $args );
	}


	/**
	 * Get attributes defined in this class as well
	 * as common attributes shared by all blocks.
	 *
	 * @phpstan-return array<string, ATTR_SHAPE>
	 * @return array
	 */
	protected function get_all_attributes() {
		return \array_merge( [
			'clientId'             => [
				'type' => 'string',
			],
			self::RENDER_REQUEST => [
				'type' => 'boolean',
			],
			'sidebarId'            => [
				'type' => 'string',
			],
			'style'                => [
				'type' => 'object',
			],
			Menu_Abstract::TITLE   => [
				'type' => 'string',
			],
		], $this->get_attributes() );
	}


	/**
	 * Include this block's id and attributes in the JS config.
	 *
	 * @param array $config - JS config in current state.
	 *
	 * @filter advanced-sidebar-menu/pro-scripts/js-config
	 *
	 * @return array
	 */
	public function js_config( array $config ) {
		$config['blocks'][ \explode( '/', static::NAME )[1] ] = [
			'id' => static::NAME,
			'attributes' => $this->get_all_attributes(),
			'supports'   => $this->get_block_support(),
		];

		return $config;
	}


	/**
	 * Checkboxes are saved as `true` on the Gutenberg side.
	 * The widgets expect the values to be `checked`.
	 *
	 * @param array<string, mixed> $attr - Attribute values pre-converted.
	 *
	 * @return array<string, mixed>
	 */
	public function convert_checkbox_values( array $attr ): array {
		// Map the boolean values to widget style 'checked'.
		return Utils::instance()->array_map_recursive( function( $value ) {
			if ( true === $value ) {
				return 'checked';
			}
			if ( false === $value ) {
				return '';
			}
			return $value;
		}, $attr );
	}


	/**
	 * Within the Editor ServerSideRender request come in as REST requests.
	 * We spoof the WP_Query as much as required to get the menus to
	 * display the same way they will on the front-end.
	 */
	protected function spoof_wp_query(): void {
		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		add_action( 'advanced-sidebar-menu/widget/before-render', function( Menu_Abstract $menu ) {
			if ( method_exists( $menu, 'set_current_post' ) ) {
				$menu->set_current_post( get_post() );
			}
		} );
		add_filter( 'advanced-sidebar-menu/core/include-template-parts-comments', '__return_false' );
		$GLOBALS['wp_query']->queried_object = $post;
		$GLOBALS['wp_query']->queried_object_id = $post->ID;
		$GLOBALS['wp_query']->is_singular = true;
		if ( 'page' === get_post_type() ) {
			$GLOBALS['wp_query']->is_page = true;
		} else {
			$GLOBALS['wp_query']->is_single = true;
		}
	}


	/**
	 * Render the block by passing the attributes to the widget renders.
	 *
	 * @phpstan-param \Union<SETTINGS, SHARED> $attr
	 *
	 * @param array $attr - Block attributes matching widget settings.
	 *
	 * @return string
	 */
	public function render( $attr ) {
		if ( \defined( 'REST_REQUEST' ) && REST_REQUEST && ! Utils::instance()->is_empty( $attr, self::RENDER_REQUEST ) ) {
			$this->spoof_wp_query();
		}

		// Use the sidebar arguments if available.
		if ( isset( $attr['sidebarId'], $GLOBALS['wp_registered_sidebars'][ $attr['sidebarId'] ] ) && '' !== $attr['sidebarId'] ) {
			// @phpstan-ignore-next-line -- Until we can support @template in `\Union` we can't properly type this.
			$this->widget_args = wp_parse_args( (array) $GLOBALS['wp_registered_sidebars'][ $attr['sidebarId'] ], $this->widget_args );
		}

		$classnames = '';
		if ( class_exists( Style_Targeting::class ) && isset( $attr[ Style_Targeting::BLOCK_STYLE ] ) && true === $attr[ Style_Targeting::BLOCK_STYLE ] ) {
			$classnames .= ' advanced-sidebar-blocked-style';
		}

		if ( ! Utils::instance()->is_empty( $this->widget_args, 'before_widget' ) ) {
			// Add main CSS class to widgets wrap.
			if ( false !== \strpos( $this->widget_args['before_widget'], 'widget_block' ) ) {
				$this->widget_args['before_widget'] = \str_replace( 'widget_block', 'widget_block advanced-sidebar-menu', $this->widget_args['before_widget'] );
			} else {
				$classnames .= ' advanced-sidebar-menu';
			}
			// Widgets already have a `<section>` wrap.
			$wrap = 'div';
		} else {
			$classnames .= ' advanced-sidebar-menu';
			$wrap = 'section';
		}

		/**
		 * Filter the attributes used in the block wrapper.
		 *
		 * @since 9.4.2
		 *
		 * @param array<string, string> $attributes - Attributes to add to the wrapper.
		 * @param array<string, mixed>  $attr       - Block attributes.
		 * @param string                $classnames - CSS Class names to add to the wrapper.
		 * @param Block_Abstract        $block      - Block instance.
		 */
		$attributes = apply_filters( 'advanced-sidebar-menu/block-wrapper-attributes/' . static::NAME, [
			'class' => \trim( esc_attr( $classnames ) ),
		], $attr, $classnames, $this );

		$wrapper_attributes = get_block_wrapper_attributes( $attributes );
		$this->widget_args['before_widget'] .= \sprintf( '<%s %s>', $wrap, $wrapper_attributes );
		$this->widget_args['after_widget'] = \sprintf( '</%s>', $wrap ) . $this->widget_args['after_widget'];
		// Passed via ServerSideRender, so we can enable accordions in Gutenberg editor.
		if ( isset( $attr['clientId'] ) && '' !== \trim( $attr['clientId'] ) ) {
			$this->widget_args['widget_id'] = $attr['clientId'];
		}

		ob_start();
		$widget = $this->get_widget_class();
		$widget->widget( $this->widget_args, $this->convert_checkbox_values( $attr ) );
		return (string) ob_get_clean();
	}
}
