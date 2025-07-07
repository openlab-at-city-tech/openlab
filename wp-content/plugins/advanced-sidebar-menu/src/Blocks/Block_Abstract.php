<?php

namespace Advanced_Sidebar_Menu\Blocks;

use Advanced_Sidebar_Menu\__Temp_Id_Proxy;
use Advanced_Sidebar_Menu\Blocks\Register\Attribute;
use Advanced_Sidebar_Menu\Blocks\Register\Register_Utils;
use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Scripts;
use Advanced_Sidebar_Menu\Utils;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;
use Advanced_Sidebar_Menu\Widget\Widget;
use Advanced_Sidebar_Menu\Widget\WidgetWithId;
use Advanced_Sidebar_Menu\Widget_Options\Shared\Style_Targeting;

/**
 * Functionality shared by and required by all blocks.
 *
 * @since 9.0.0
 *
 * @phpstan-import-type PAGE_SETTINGS from Page
 * @phpstan-import-type CATEGORY_SETTINGS from Category
 * @phpstan-import-type WIDGET_ARGS from Widget
 *
 * @phpstan-type ATTR_SHAPE array{
 *    type: Attribute::TYPE_*|(list<Attribute::TYPE_*>),
 *    default?: mixed,
 *    enum?: list<string|int|bool>,
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
 * @phpstan-template WIDGET_SETTINGS of array<string, string|int|array<string, string>>
 * @phpstan-template DEFAULTS of array<key-of<SETTINGS>, int|string|array<string, string>>
 */
abstract class Block_Abstract {
	public const NAME = 'block-abstract';

	public const RENDER_REQUEST = 'isServerSideRenderRequest';
	public const BLOCK_ID       = 'clientId';
	public const SIDEBAR_ID     = 'sidebarId';

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
	protected array $widget_args = [
		'before_widget' => '',
		'after_widget'  => '',
		// Default used for FSE.
		'before_title'  => '<h2 class="advanced-sidebar-menu-title">',
		'after_title'   => '</h2>',
	];


	/**
	 * @todo  Remove once minimum required PRO Blocks\Navigation implements `Block` interface.
	 *
	 * @return array<string, ATTR_SHAPE|Attribute>
	 */
	abstract protected function get_attributes();


	/**
	 * @todo  Remove once minimum required PRO Blocks\Navigation implements `Block` interface.
	 *
	 * @return WidgetWithId<WIDGET_SETTINGS, DEFAULTS>
	 */
	abstract protected function get_widget_class();


	/**
	 * @todo  Remove once \Advanced_Sidebar_Menu\Blocks\Navigation implements `Block` interface.
	 *
	 * @return string
	 */
	abstract protected function get_description();


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
	 * instead of new widgets.
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
		$blocks[] = __Temp_Id_Proxy::factory( $widget )->get_id_base();
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
	 * @phpstan-param WIDGET_ARGS                         $args
	 *
	 * @param false|array                                 $instance - Contents of the block, before parsing.
	 * @param \WP_Widget<PAGE_SETTINGS|CATEGORY_SETTINGS> $widget   - Object representing a block-based widget.
	 * @param array                                       $args     - Widget area arguments.
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
		$attributes = \array_merge(
			Common::instance()->get_common_attributes(),
			Common::instance()->get_server_side_render_attributes(),
			$this->get_attributes()
		);

		$args = apply_filters( 'advanced-sidebar-menu/block-register/' . static::NAME, [
			'api_version'           => 3,
			'attributes'            => Register_Utils::instance()->translate_attributes_to_php( $attributes ),
			'description'           => $this->get_description(),
			'editor_script_handles' => [ Scripts::GUTENBERG_HANDLE ],
			'editor_style_handles'  => [ Scripts::GUTENBERG_CSS_HANDLE ],
			'render_callback'       => [ $this, 'render' ],
			'supports'              => Common::instance()->get_block_supports(),
		] );

		register_block_type( static::NAME, $args );
	}


	/**
	 * @deprecated 9.7.0
	 *
	 * @phpstan-return array<string, ATTR_SHAPE>
	 */
	protected function get_all_attributes() {
		_deprecated_function( __METHOD__, '9.7.0' );
		$all = \array_merge(
			Common::instance()->get_common_attributes(),
			Common::instance()->get_server_side_render_attributes(),
			$this->get_attributes()
		);
		return Register_Utils::instance()->translate_attributes_to_php( $all );
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
		if ( $this instanceof Block ) {
			$name = $this->get_name();
		} else {
			$name = static::NAME;
		}

		$config['blocks'][ \explode( '/', $name )[1] ] = [
			'id'         => $name,
			'attributes' => Register_Utils::instance()->translate_attributes_to_php( $this->get_attributes() ),
		];

		return $config;
	}


	/**
	 * Checkboxes are saved as `true` on the Gutenberg side.
	 * The widgets expect the values to be `checked`.
	 *
	 * @phpstan-param \Union<SETTINGS, SHARED>          $attr
	 *
	 * @param array<string, mixed|array<string, mixed>> $attr - Attribute values pre-converted.
	 *
	 * @phpstan-return WIDGET_SETTINGS
	 * @return array<string, string|int|array<string, string|int>>
	 */
	public function convert_checkbox_values( array $attr ): array {
		// Map the boolean values to the widget style 'checked'.
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
	 * Is the current render a `ServerSideRender` request?
	 *
	 * @since 9.7.0
	 *
	 * @todo  Switch to `wp_is_rest_endpoint` once WP 6.5 is the minimum.
	 *
	 * @phpstan-param \Union<SETTINGS, SHARED> $attr
	 *
	 * @param array                            $attr - Block attributes matching widget settings.
	 *
	 * @return bool
	 */
	protected function is_server_side_render( array $attr ): bool {
		//phpcs:ignore WordPress.NamingConventions -- Temporarily using core filter from wp_is_rest_request.
		$is_rest = (bool) apply_filters( 'wp_is_rest_endpoint', \defined( 'REST_REQUEST' ) && REST_REQUEST );
		return $is_rest && ! Utils::instance()->is_empty( $attr, self::RENDER_REQUEST );
	}


	/**
	 * When `ServerSideRender` is passed `skipBlockSupportAttributes` it removes
	 * all styles added to the block wrapper in the editor. The `shadow` style
	 * has not yet been added to the remove list, so we must do it manually,
	 * or the box shadow will double up.
	 *
	 * @link https://github.com/WordPress/gutenberg/issues/65882
	 * @todo Remove when the issue is resolved.
	 *
	 * @internal
	 *
	 * @param string $wrapper_attributes - Attributes to add to the wrapper.
	 *
	 * @return string
	 */
	private function strip_box_shadow( string $wrapper_attributes ): string { //phpcs:ignore LipePlugin.CodeAnalysis.PrivateInClass -- Temporary method.
		$wrapper_attributes = (string) \preg_replace( '/box-shadow:[^;]+;?/', '', $wrapper_attributes );
		return \str_replace( 'style="" ', '', $wrapper_attributes );
	}


	/**
	 * Render the block by passing the attributes to the widget renders.
	 *
	 * @phpstan-param \Union<SETTINGS, SHARED> $attr
	 *
	 * @param array                            $attr - Block attributes matching widget settings.
	 *
	 * @return string
	 */
	public function render( array $attr ): string {
		if ( $this->is_server_side_render( $attr ) ) {
			$this->spoof_wp_query();
		}

		$widget_args = $this->widget_args;

		// Use the sidebar arguments if available.
		if ( isset( $attr[ self::SIDEBAR_ID ], $GLOBALS['wp_registered_sidebars'][ $attr[ self::SIDEBAR_ID ] ] ) && '' !== $attr[ self::SIDEBAR_ID ] ) {
			$widget_args = wp_parse_args( (array) $GLOBALS['wp_registered_sidebars'][ $attr[ self::SIDEBAR_ID ] ], $widget_args );
		}

		$classnames = '';
		if ( \class_exists( Style_Targeting::class ) && isset( $attr[ Style_Targeting::BLOCK_STYLE ] ) && true === $attr[ Style_Targeting::BLOCK_STYLE ] ) {
			$classnames .= ' advanced-sidebar-blocked-style';
		}

		if ( ! Utils::instance()->is_empty( $widget_args, 'before_widget' ) ) {
			// Add the main CSS class to widgets wrap.
			if ( false !== \strpos( $this->widget_args['before_widget'], 'widget_block' ) ) {
				$widget_args['before_widget'] = \str_replace( 'widget_block', 'widget_block advanced-sidebar-menu', $widget_args['before_widget'] );
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
		if ( $this->is_server_side_render( $attr ) ) {
			$wrapper_attributes = $this->strip_box_shadow( $wrapper_attributes );
		}
		$widget_args['before_widget'] .= \sprintf( '<%s %s>', $wrap, $wrapper_attributes );
		$widget_args['after_widget'] = \sprintf( '</%s>', $wrap ) . $widget_args['after_widget'];
		// Passed via ServerSideRender, so we can enable accordions in the Gutenberg editor.
		if ( isset( $attr[ self::BLOCK_ID ] ) && '' !== \trim( $attr[ self::BLOCK_ID ] ) ) {
			$widget_args['widget_id'] = $attr[ self::BLOCK_ID ];
		}

		\ob_start();
		$widget = $this->get_widget_class();
		$widget->widget( $widget_args, $this->convert_checkbox_values( $attr ) );
		return (string) \ob_get_clean();
	}
}
