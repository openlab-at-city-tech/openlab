<?php

namespace ColibriWP\Theme\Components\Header;

use ColibriWP\Theme\Core\PartialComponent;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class NavBarStyle extends PartialComponent {

	protected static $instances = array();

	protected $prefix   = 'header_front_page.navigation.';
	protected $selector = '';

	public function __construct( $prefix, $selector ) {
		$this->prefix   = $prefix;
		$this->selector = $selector . ':not(.extra-1)';
	}

	public static function getInstance( $prefix, $selector ) {
		if ( ! isset( static::$instances[ "{$prefix}_{$selector}" ] ) ) {
			static::$instances[ "{$prefix}_{$selector}" ] = new static( $prefix, $selector );
		}

		return static::$instances[ "{$prefix}_{$selector}" ];
	}

	public function getOptions() {
		$prefix      = $this->getPrefix();
		$section     = 'nav_bar';
		$colibri_tab = 'content';
		$priority    = 10;

		return array(
			'settings' => array(

				"{$prefix}props.heroSection.layout" => array(
					'default'    => Defaults::get( "{$prefix}props.heroSection.layout" ),
					'control'    => array(
						'label'       => Translations::get( 'layout_type' ),
						'focus_alias' => 'navigation',
						'type'        => 'select-icon',
						'section'     => $section,
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
						'choices'     => array(
							'logo-spacing-menu' =>
								array(
									'tooltip' => Translations::get( 'logo_nav' ),
									'label'   => Translations::get( 'logo_nav' ),
									'value'   => 'logo-spacing-menu',
									'icon'    => Defaults::get( 'icons.logoNav.content' ),
								),

							'logo-above-menu'   =>
								array(
									'tooltip' => Translations::get( 'logo_above' ),
									'label'   => Translations::get( 'logo_above' ),
									'value'   => 'logo-above-menu',
									// 'icon'    => $icons['logoAbove']['content'],
									'icon'    => Defaults::get( 'icons.logoAbove.content' ),
								),
						),
					),
					'css_output' => array(
						array(
							'selector' => "{$this->selector} .h-column-container",
							'property' => 'flex-basis',
							'value'    => array(
								'logo-spacing-menu' => 'auto',
								'logo-above-menu'   => '100%',
							),
						),
						array(
							'selector' => "{$this->selector} .h-column-container:nth-child(1) a",
							'property' => 'margin',
							'value'    => array(
								'logo-spacing-menu' => 'auto',
								'logo-above-menu'   => 'auto',
							),
						),
						array(
							'selector' => "{$this->selector} .h-column-container:nth-child(2)",
							'property' => 'display',
							'value'    => array(
								'logo-spacing-menu' => 'block',
								'logo-above-menu'   => 'block',
							),
						),
						array(
							'selector' => "{$this->selector} div > .colibri-menu-container > ul.colibri-menu",
							'property' => 'justify-content',
							'value'    => array(
								'logo-spacing-menu' => 'normal',
								'logo-above-menu'   => 'center',
							),
						),

					),
					/*
					  'js_output' => array(
						array(
							'selector' => "{$this->selector}#navigation",
							'action'   => 'colibri-component-restart',
							'value'    => 'navigation'
						),
					)*/
				),

				"{$prefix}separator1"               => array(
					'default' => '',
					'control' => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'nav_bar',
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
					),
				),

				"{$prefix}props.sticky"             => array(
					'default'   => Defaults::get( "{$prefix}props.sticky" ),
					'control'   => array(
						'label'       => Translations::get( 'stick_to_top' ),
						'type'        => 'switch',
						'section'     => $section,
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
					),
					'js_output' => array(
						array(
							'selector' => "{$this->selector}#navigation",
							'action'   => 'colibri-navigation-toggle-sticky',
						),

					),

				),

				"{$prefix}separator2"               => array(
					'default' => '',
					'control' => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'nav_bar',
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
					),
				),

				"{$prefix}props.width"              => array(
					'default'   => Defaults::get( "{$prefix}props.width" ),
					'control'   => array(
						'label'       => Translations::get( 'container_width' ),
						'section'     => $section,
						'type'        => 'button-group',
						'button_size' => 'medium',
						'choices'     => array(
							'boxed'      => Translations::escHtml( 'boxed' ),
							'full-width' => Translations::escHtml( 'full_width' ),
						),
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
						'none_value'  => '',

					),
					'js_output' => array(
						array(
							'selector' => '.style-local-' . Defaults::get( "{$this->prefix}.nodeId" ) . '-outer',
							'action'   => 'set-class',
							'value'    => array(
								'boxed'      => 'kubio-theme-nav-boxed',
								'full-width' => 'kubio-theme-nav-full-width',
							),
						),
						array(
							'selector' => "{$this->selector} .h-section-boxed-container, {$this->selector} .h-section-fluid-container",
							'action'   => 'set-class',
							'value'    => array(
								'boxed'      => 'h-section-boxed-container',
								'full-width' => 'h-section-fluid-container',
							),
						),
					),
				),

				"{$prefix}style.padding.top.value"  => array(
					'default'    => Defaults::get( "{$prefix}style.padding.top.value" ),
					'control'    => array(
						'label'       => Translations::get( 'navigation_padding' ),
						'type'        => 'slider',
						'section'     => $section,
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
						'min'         => 0,
						'max'         => 120,
					),
					'css_output' => array(
						array(
							'selector'      => $this->selector . '[data-kubio-component="navigation"]#navigation',
							'property'      => 'padding-top',
							'value_pattern' => '%spx',
						),
						array(
							'selector'      => $this->selector . '[data-kubio-component="navigation"]#navigation',
							'property'      => 'padding-bottom',
							'value_pattern' => '%spx',
						),
					),
				),
				// add hidden input to show edit element button
				"{$prefix}hidden"                   => array(
					'default' => '',
					'control' => array(
						'label'       => '',
						'type'        => 'hidden',
						'section'     => 'nav_bar',
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
					),
				),
			),
		);
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	public function renderContent( $parameters = array() ) {
		$this->addFrontendJSData(
			Defaults::get( $this->getPrefix() . 'nodeId', 'no_component' ),
			array(
				'data' => array(
					'overlap' => true,
				),
			)
		);

		if ( $style = $this->getNavLayoutStyle() ) {
			printf( '<style>%s</style>', $style );
		}
	}

	public function getNavLayoutStyle() {
		$layoutType = $this->mod( 'props.heroSection.layout' );
		$css        = '';

		switch ( $layoutType ) {
			case 'logo-above-menu':
				$css .= "{$this->selector} .h-column-container  { flex-basis: 100%; }" .
				"{$this->selector} .h-column-container:nth-child(1) a { margin: auto; }" .
				"{$this->selector} div > .colibri-menu-container > ul.colibri-menu { justify-content: center; }";
				break;
		}

		return $css;
	}

	public function mod( $name ) {
		return parent::mod( $this->getPrefix() . $name );
	}
}
