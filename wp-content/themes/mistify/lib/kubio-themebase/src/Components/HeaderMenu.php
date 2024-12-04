<?php


namespace Kubio\Theme\Components;

use ColibriWP\Theme\Components\Header\HeaderMenu as ColibriHeaderMenu;

class HeaderMenu extends ColibriHeaderMenu {

	const PARENT_MENU_ARROW           = '<svg class="kubio-menu-item-icon" role="img" viewBox="0 0 320 512">' .
							  '	<path d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path>' .
							  '</svg>';
	protected static $settings_prefix = 'front-header.header-menu.';

	public function printHeaderMenu() {
		add_filter( 'nav_menu_link_attributes', array( $this, 'menuItemsAttrs' ), 2, 4 );
		add_filter( 'nav_menu_item_args', array( $this, 'addParentMenuItemsIcon' ), 2, 3 );
		wp_nav_menu(
			array(
				'container'      => false,
				'theme_location' => 'header-menu',
				'fallback_cb'    => array( $this, 'fallbackMenu' ),
				'menu_class'     => 'menu kubio-has-gap-fallback',
			)
		);

		remove_filter( 'nav_menu_link_attributes', array( $this, 'menuItemsAttrs' ), 2, 4 );
		remove_filter( 'nav_menu_item_args', array( $this, 'addParentMenuItemsIcon' ), 2, 3 );
	}


	public function fallbackMenu() {
		$pages_menu = wp_page_menu(
			array(
				'container' => 'kubio-pages-element',
				'echo'      => false,
			)
		);

		$pages_menu = preg_replace( '#<kubio-pages-element.*?>#', '', $pages_menu );
		$pages_menu = preg_replace( '#</kubio-pages-element>#', '', $pages_menu );
		$pages_menu = preg_replace( '#<ul>#', '<ul class="menu kubio-has-gap-fallback">', $pages_menu );

		echo $pages_menu;
	}

	public function addParentMenuItemsIcon( $args, $item, $depth ) {
		if ( in_array( 'menu-item-has-children', $item->classes, true ) ) {
			$args->link_before = '<span>';
			$args->link_after  = '</span>' . self::PARENT_MENU_ARROW;
		} else {
			$args->link_before = '';
			$args->link_after  = '';
		}

		return $args;
	}

	public function menuItemsAttrs( $atts, $item, $args, $depth ) {
		$style = isset( $atts['style'] ) ? $atts['style'] : '';

		$depth_value = min( array( $depth, 4 ) );

		$style .= ";--kubio-menu-item-depth:{$depth_value}";

		$atts['style'] = $style;

		return $atts;
	}

	public function printHeaderMenuClasses() {

		$prefix  = static::$settings_prefix;
		$classes = static::mod( "{$prefix}props.hoverEffect.type", '' );

		if ( $classes !== 'none' && $classes != 'solid-active-item' && strpos( $classes, 'bordered-active-item' ) !== - 1 ) {
			$classes .= ' bordered-active-item ';
		}

		$classes .= ' ' . static::mod( "{$prefix}props.hoverEffect.group.border.transition" );
		$classes .= ' ' . static::mod( "{$prefix}props.showOffscreenMenuOn" );

		return $classes;
	}

}
