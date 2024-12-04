<?php

namespace ColibriWP\Theme\Components\FrontHeader;

use ColibriWP\Theme\AssetsManager;
use ColibriWP\Theme\Components\Header\NavBarStyle;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\View;

class NavBar extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.navigation.';

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$style = static::style()->getOptions();

		return $style;
	}

	/**
	 * @return NavBarStyle
	 */
	public static function style() {
		return NavBarStyle::getInstance( static::getPrefix(), static::selectiveRefreshSelector() );
	}

	protected static function getPrefix() {
		return static::$settings_prefix;
	}

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::getPrefix() . 'selective_selector', false );
	}


	public function renderContent( $parameters = array() ) {
		static::style()->renderContent();
		View::partial(
			'front-header',
			'navigation',
			array(
				'component' => $this,
			)
		);
	}

	public function printHeaderMenu() {
		View::printMenu(
			array(
				'id'      => 'header-menu',
				'classes' => 'none',
			)
		);
	}

	public function printWrapperClasses() {
		$classes = array( 'navigation-wrapper' );
		$prefix  = static::getPrefix();

		if ( $this->mod( "{$prefix}boxed_navigation", false ) ) {
			$classes[] = 'gridContainer';
		}

		echo esc_attr( implode( ' ', $classes ) );
	}

	public function printNavigationClasses() {
		$classes = array();
		$prefix  = static::getPrefix();

		if ( $this->mod( "{$prefix}props.overlap", Defaults::get( "{$prefix}props.overlap", true ) ) ) {
			$classes[] = 'h-navigation_overlap';
		}
		if ( $width = $this->mod( "{$prefix}props.width", 'boxed' ) ) {
			$classes[] = "kubio-theme-nav-{$width}";
		}

		echo esc_attr( implode( ' ', $classes ) );
	}

	public function printNavigationTypeClasses() {
		$prefix      = static::getPrefix();
		$layout_type = $this->mod( "{$prefix}props.layoutType" );

		if ( $layout_type === 'logo-spacing-menu' ) {
			return 'has-logo-spacing-menu';
		} elseif ( $layout_type === 'logo-above-menu' ) {
			return 'has-logo-above-menu';
		}

		return '';
	}

	public function printContainerClasses() {
		$classes = array();
		$prefix  = static::getPrefix();

		$width_options = array(
			'boxed'      => 'h-section-boxed-container',
			'full-width' => 'h-section-fluid-container',
		);

		if ( $width = $this->mod( "{$prefix}props.width", 'boxed' ) ) {
			$classes[] = $width_options[ $width ];
		}

		echo esc_attr( implode( ' ', $classes ) );
	}
}
