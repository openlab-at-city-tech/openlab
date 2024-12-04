<?php

namespace ColibriWP\Theme\Components\Header;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class HeaderMenu extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.header-menu.';
	private $attrs                    = array();

	private $has_children = array();

	public function __construct() {
		$prefix  = static::$settings_prefix;
		$classes = static::mod( "{$prefix}props.hoverEffect.type" );

		if ( $classes != 'solid-active-item' && strpos( $classes, 'bordered-active-item' ) !== - 1 ) {
			$classes .= ' bordered-active-item ';
		}

		$classes .= ' ' . static::mod( "{$prefix}props.hoverEffect.group.border.transition" );

		$defaultAttrs = array(
			'id'                 => 'header-menu',
			'classes'            => $classes,
			'show_shopping_cart' => '0',
		);

		$this->attrs = $defaultAttrs;
	}

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::$settings_prefix . 'selective_selector', false );
	}

	protected static function getOptions() {
		$prefix   = static::$settings_prefix;
		$settings = array_merge(
			static::getContentOptions(),
			static::getStyleOptions()
		);

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'menu' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => $settings,
		);
	}

	/**
	 * @return array();
	 */
	protected static function getContentOptions() {
		$prefix                  = static::$settings_prefix;
		$selector                = '[data-kubio-component="dropdown-menu"]';
		$hamburger_menu_selector = "[data-kubio='kubio/menu-offscreen']";

		$menu_choices = array( 0 => Translations::get( 'no_menu' ) );
		$menus        = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			$menu_choices[ (string) $menu->term_id ] = $menu->name;
		}

		return array(

			"{$prefix}edit"                      => array(
				'default'   => Defaults::get( "{$prefix}value" ),
				'control'   => array(
					'label'       => Translations::get( 'edit_menu_structure' ),
					'type'        => 'button',
					'section'     => "{$prefix}section",
					'colibri_tab' => 'content',
				),
				'js_output' => array(
					array(
						'selector' => '#navigation',
						'action'   => 'focus',
						'value'    => array(
							'entity'    => 'panel',
							'entity_id' => 'nav_menus',
						),
					),
				),
			),

			"{$prefix}style.descendants.innerMenu.justifyContent" => array(
				'default'    => Defaults::get( "{$prefix}style.descendants.innerMenu.justifyContent", 'end' ),
				'control'    => array(
					'label'       => Translations::escHtml( 'button_align' ),
					'focus_alias' => 'menu',
					'type'        => 'align-button-group',
					'button_size' => 'medium',
					'choices'     => array(
						'flex-start' => 'left',
						'center'     => 'center',
						'flex-end'   => 'right',
					),
					'none_value'  => 'flex-start',
					'section'     => "{$prefix}section",
					'colibri_tab' => 'content',
				),
				'css_output' => array(
					array(
						'selector'      => "$selector ul, $hamburger_menu_selector >div",
						'media'         => CSSOutput::NO_MEDIA,
						'property'      => 'justify-content',
						'value_pattern' => '%s !important',
					),
				),
			),

			"{$prefix}props.showOffscreenMenuOn" => array(
				'default' => Defaults::get( "{$prefix}props.showOffscreenMenuOn" ),
				'control' => array(
					'label'       => Translations::get( 'show_offscreen_menu_on' ),
					'type'        => 'select',
					'section'     => "{$prefix}section",
					'colibri_tab' => 'content',
					'choices'     => array(
						'has-offcanvas-mobile'  => Translations::escHtml( 'mobile' ),
						'has-offcanvas-tablet'  => Translations::escHtml( 'mobile_tablet' ),
						'has-offcanvas-desktop' => Translations::escHtml( 'mobile_tablet_desktop' ),
					),
				),
			),
		);
	}

	/**
	 * @return array();
	 */
	protected static function getStyleOptions() {
		$prefix   = static::$settings_prefix;
		$selector = '[data-kubio-component="dropdown-menu"]';

		return array(

			"{$prefix}props.hoverEffect.type"        => array(
				'default' => Defaults::get( "{$prefix}props.hoverEffect.type" ),
				'control' => array(
					'transport'   => 'selective_refresh',
					'label'       => Translations::get( 'button_highlight_type' ),
					'type'        => 'select',
					'linked_to'   => "{$prefix}props.hoverEffect.group.border.transition",
					'section'     => "{$prefix}section",
					'colibri_tab' => 'style',
					'choices'     => array(
						'none' => Translations::escHtml( 'none' ),
						'bordered-active-item bordered-active-item--bottom' => Translations::escHtml( 'bottom_line' ),
					),
				),
			),

			"{$prefix}props.hoverEffect.activeGroup" => array(
				'default' => 'border',
				'control' => array(
					'label'       => '&nbsp;',
					'type'        => 'hidden',
					'section'     => "{$prefix}section",
					'colibri_tab' => 'style',
				),
			),

			"{$prefix}props.hoverEffect.group.border.transition" => array(
				'default' => Defaults::get( "{$prefix}props.hoverEffect.group.border.transition" ),
				'control' => array(
					'label'        => Translations::get( 'button_hover_effect' ),
					'type'         => 'linked-select',
					'linked_to'    => "{$prefix}props.hoverEffect.type",
					'section'      => "{$prefix}section",
					'colibri_tab'  => 'style',
					'transport'    => 'selective_refresh',
					'active_rules' => array(
						array(
							'setting'  => "{$prefix}props.hoverEffect.type",
							'operator' => '!=',
							'value'    => 'none',
						),
					),
					'choices'      =>
						array(
							'bordered-active-item bordered-active-item--bottom' => array(
								'effect-none'        => Translations::escHtml( 'none' ),
								'effect-borders-in'  => Translations::escHtml( 'drop_in' ),
								'effect-borders-out' => Translations::escHtml( 'drop_out' ),
								'effect-borders-grow grow-from-left' => Translations::escHtml( 'grow_from_left' ),
								'effect-borders-grow grow-from-right' => Translations::escHtml( 'grow_from_right' ),
								'effect-borders-grow grow-from-center' => Translations::escHtml( 'grow_from_center' ),
							),
							'bordered-active-item bordered-active-item--top' => array(
								'effect-none'        => Translations::escHtml( 'none' ),
								'effect-borders-in'  => Translations::escHtml( 'drop_in' ),
								'effect-borders-out' => Translations::escHtml( 'drop_out' ),
								'effect-borders-grow grow-from-left' => Translations::escHtml( 'grow_from_left' ),
								'effect-borders-grow grow-from-right' => Translations::escHtml( 'grow_from_right' ),
								'effect-borders-grow grow-from-center' => Translations::escHtml( 'grow_from_center' ),
							),
							'bordered-active-item bordered-active-item--top-and-bottom' => array(
								'effect-none'        => Translations::escHtml( 'none' ),
								'effect-borders-in'  => Translations::escHtml( 'drop_in' ),
								'effect-borders-out' => Translations::escHtml( 'drop_out' ),
								'effect-borders-grow grow-from-left' => Translations::escHtml( 'grow_from_left' ),
								'effect-borders-grow grow-from-right' => Translations::escHtml( 'grow_from_right' ),
								'effect-borders-grow grow-from-center' => Translations::escHtml( 'grow_from_center' ),
							),
							'solid-active-item' => array(
								'solid-active-item effect-none'                    => Translations::escHtml( 'none' ),
								'solid-active-item effect-pull-up'                 => Translations::escHtml( 'grow_up' ),
								'solid-active-item effect-pull-down'               => Translations::escHtml( 'grow_down' ),
								'solid-active-item effect-pull-left'               => Translations::escHtml( 'grow_left' ),
								'solid-active-item effect-pull-right'              => Translations::escHtml( 'grow_right' ),
								'solid-active-item effect-pull-up-down'            => Translations::escHtml( 'shutter_in_horizontal' ),
								'solid-active-item effect-pull-up-down-reverse'    => Translations::escHtml( 'shutter_out_horizontal' ),
								'solid-active-item effect-pull-left-right'         => Translations::escHtml( 'shutter_in_vertical' ),
								'solid-active-item effect-pull-left-right-reverse' => Translations::escHtml( 'shutter_out_vertical' ),
							),
						),
				),
			),
		);
	}

	public function getPenPosition() {
		return static::PEN_ON_LEFT;
	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'front-header',
			'header-menu',
			array(
				'component' => $this,
			)
		);
	}

	public function printHeaderMenu() {
		$theme_location         = $this->attrs['id'];
		$customClasses          = $this->attrs['classes'];
		$drop_down_menu_classes = array( 'colibri-menu' );
		$drop_down_menu_classes = array_merge( $drop_down_menu_classes, array( $customClasses ) );

		$self = $this;
		add_filter(
			'nav_menu_item_title',
			function ( $title, $item, $args, $depth ) use ( $self ) {
				return $self->addFirstLevelIcons( $title, $item );
			},
			10,
			4
		);

		wp_nav_menu(
			array(
				'echo'            => true,
				'theme_location'  => $theme_location,
				'menu_class'      => esc_attr( implode( ' ', $drop_down_menu_classes ) ),
				'container_class' => 'colibri-menu-container',
				'fallback_cb'     => array( $this, 'colibriNomenuFallback' ),
			)
		);

		$key = static::$settings_prefix . 'nodeId';

		$this->addFrontendJSData(
			Defaults::get( $key, false ),
			array(
				'data' => array(
					'type' => 'horizontal',
				),
			)
		);

	}

	public function addFirstLevelIcons( $title, $item ) {
		$arrow = $this->getMenuArrows();

		if ( is_numeric( $item ) ) {

			if ( wp_get_post_parent_id( $item ) ) {
				return $title;
			}

			$args = array(
				'post_parent'   => $item, // Current post's ID
				'post_type__in' => 'post,page',
				'numberposts'   => 1,
				'fields'        => 'ids',
			);

			if ( ! array_key_exists( $item, $this->has_children ) ) {
				$children                    = get_children( $args );
				$this->has_children[ $item ] = ! empty( $children );
			}

			if ( $this->has_children[ $item ] ) {
				return $title . $arrow;
			}
		} else {

			if ( $item instanceof \WP_Post && $item->post_type === 'nav_menu_item' ) {
				if ( intval( $item->post_parent ) ) {
					return $title;
				}
			}
		}

		if ( is_object( $item ) && in_array( 'menu-item-has-children', $item->classes ) && ! $item->menu_item_parent ) {
			// down arrow
			return $title . $arrow;
		}

		return $title;
	}

	private function getMenuArrows() {
		$arrow = '';

		// down arrow
		$arrow = '' .
			   ' <svg aria-hidden="true" data-prefix="fas" data-icon="angle-down" class="svg-inline--fa fa-angle-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">' .
			   '  <path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path>' .
			   ' </svg>' .
			   '';

		// right arrow
		$arrow .= '' .
				' <svg aria-hidden="true" data-prefix="fas" data-icon="angle-right" class="svg-inline--fa fa-angle-right fa-w-8" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512">' .
				'  <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>' .
				' </svg>' .
				'';

		return $arrow;
	}

	public function hasOffCanvasMobile() {
		$prefix = static::$settings_prefix;
		$type   = static::mod( "{$prefix}props.hoverEffect.type" );

		return ( $type == 'none' ) ? 'has-offcanvas-mobile' : '';
	}

	public function printContainerClasses() {
		$prefix            = static::$settings_prefix;
		$container_classes = static::mod( "{$prefix}props.showOffscreenMenuOn" );

		echo esc_attr( $container_classes );
	}

	public function printNavigationTypeClasses() {
		$prefix      = static::$settings_prefix;
		$layout_type = $this->mod( "{$prefix}props.layoutType" );

		if ( $layout_type === 'logo-spacing-menu' ) {
			return 'has-logo-spacing-menu';
		} elseif ( $layout_type === 'logo-above-menu' ) {
			return 'has-logo-above-menu';
		}

		return '';
	}

	function colibriNomenuFallback() {
		$customClasses          = $this->attrs['classes'];
		$drop_down_menu_classes = array( 'colibri-menu' );
		$drop_down_menu_classes = array_merge( $drop_down_menu_classes, array( $customClasses ) );

		add_filter( 'the_title', array( $this, 'addFirstLevelIcons' ), 10, 2 );

		$menu = wp_page_menu(
			array(
				'menu_class' => 'colibri-menu-container',
				'before'     => '<ul class="' . esc_attr( implode( ' ', $drop_down_menu_classes ) ) . '">',
				'after'      => Hooks::prefixed_apply_filters( 'nomenu_after', '' ) . '</ul>',
			)
		);

		remove_filter( 'the_title', array( $this, 'addFirstLevelIcons' ), 10 );

		return $menu;
	}

	function colibriMenuAddShopingCart() {
		add_filter( 'wp_nav_menu_items', array( $this, 'colibri_woocommerce_cart_menu_item' ), 10, 2 );
		Hooks::prefixed_add_filter( 'nomenu_after', array( $this, 'colibri_woocommerce_cart_menu_item' ), 10, 2 );
	}

}
