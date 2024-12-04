<?php


namespace Kubio\Theme\Components\Common;

use ColibriWP\Theme\Components\Header\NavBarStyle;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class NavigationStyle extends NavBarStyle {

	public function getOptions() {
		$prefix      = $this->getPrefix();
		$section     = 'nav_bar';
		$colibri_tab = 'content';
		$priority    = 10;

		return array(
			'settings' => array(

				"{$prefix}props.layoutType"        => array(
					'default'    => Defaults::get( "{$prefix}props.layoutType" ),
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
					'js_output'  => array(
						array(
							'selector' => $this->selector,
							'action'   => 'set-class',
							'value'    => array(
								'logo-spacing-menu' => 'has-logo-spacing-menu',
								'logo-above-menu'   => 'has-logo-above-menu',
							),
						),
					),
					'css_output' => array(
						array(
							'selector' => "{$this->selector} .wp-block-kubio-column__container",
							'property' => 'flex-basis',
							'value'    => array(
								'logo-spacing-menu' => '',
								'logo-above-menu'   => '100%',
							),
						),
						array(
							'selector' => "{$this->selector} .wp-block-kubio-column__container:nth-child(1) a",
							'property' => 'margin',
							'value'    => array(
								'logo-spacing-menu' => '',
								'logo-above-menu'   => 'auto',
							),
						),
						array(
							'selector' => "{$this->selector} .wp-block-kubio-column__container:nth-child(2)",
							'property' => 'display',
							'value'    => array(
								'logo-spacing-menu' => '',
								'logo-above-menu'   => 'none',
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
						array(
							'selector' => "{$this->selector} .wp-block-kubio-column__container .wp-block-kubio-logo",
							'property' => 'width',
							'value'    => array(
								'logo-spacing-menu' => '',
								'logo-above-menu'   => 'fit-content',
							),
						),
					),

				),
				"{$prefix}separator2"              => array(
					'default' => '',
					'control' => array(
						'label'       => '',
						'type'        => 'separator',
						'section'     => 'nav_bar',
						'colibri_tab' => $colibri_tab,
						'priority'    => $priority ++,
					),
				),

				"{$prefix}props.width"             => array(
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
							'selector' => '.wp-block-kubio-navigation__outer',
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

				"{$prefix}style.padding.top.value" => array(
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
							'selector'      => $this->selector,
							'property'      => 'padding-top',
							'value_pattern' => '%spx',
						),
						array(
							'selector'      => $this->selector,
							'property'      => 'padding-bottom',
							'value_pattern' => '%spx',
						),
					),
				),
				// add hidden input to show edit element button
				"{$prefix}hidden"                  => array(
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

	public function renderContent( $parameters = array() ) {
		$this->addFrontendJSData(
			Defaults::get( $this->getPrefix() . 'nodeId', 'no_component' ),
			array(
				'data' => array(
					'overlap' => true,
				),
			)
		);
	}
}
