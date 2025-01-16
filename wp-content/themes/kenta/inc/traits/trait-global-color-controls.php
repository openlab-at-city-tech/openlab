<?php
/**
 * Global colors trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Facades\Css;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Global_Color_Controls' ) ) {

	/**
	 * Color controls
	 */
	trait Kenta_Global_Color_Controls {

		protected function getGlobalColorControls( $id, $selector, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'primary_color'  => Css::INITIAL_VALUE,
				'primary_active' => Css::INITIAL_VALUE,
				'accent_color'   => Css::INITIAL_VALUE,
				'accent_active'  => Css::INITIAL_VALUE,
				'base_color'     => Css::INITIAL_VALUE,
				'base_50'        => Css::INITIAL_VALUE,
				'base_100'       => Css::INITIAL_VALUE,
				'base_200'       => Css::INITIAL_VALUE,
				'base_300'       => Css::INITIAL_VALUE,
			] );

			return [
				kenta_docs_control( __( '%sRead Documentation%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/header-footer-builder/override-global-colors/' ),

				( new ColorPicker( $id . 'primary_color' ) )
					->setLabel( __( 'Primary Color', 'kenta' ) )
					->enableAlpha()
					->computedValue()
					->asyncColors( $selector, [
						'default' => '--kenta-primary-color',
						'active'  => '--kenta-primary-active',
					] )
					->addColor( 'default', __( 'Default', 'kenta' ), $defaults['primary_color'] )
					->addColor( 'active', __( 'Active', 'kenta' ), $defaults['primary_active'] )
				,
				( new ColorPicker( $id . 'accent_color' ) )
					->setLabel( __( 'Accent Color', 'kenta' ) )
					->enableAlpha()
					->computedValue()
					->asyncColors( $selector, [
						'default' => '--kenta-accent-color',
						'active'  => '--kenta-accent-active',
					] )
					->addColor( 'default', __( 'Default', 'kenta' ), $defaults['accent_color'] )
					->addColor( 'active', __( 'Active', 'kenta' ), $defaults['accent_active'] )
				,
				( new ColorPicker( $id . 'base_color' ) )
					->setLabel( __( 'Base Color', 'kenta' ) )
					->enableAlpha()
					->computedValue()
					->asyncColors( $selector, [
						'default' => '--kenta-base-color',
						'100'     => '--kenta-base-100',
						'200'     => '--kenta-base-200',
						'300'     => '--kenta-base-300',
					] )
					->addColor( '300', __( 'Base 300', 'kenta' ), $defaults['base_300'] )
					->addColor( '200', __( 'Base 200', 'kenta' ), $defaults['base_200'] )
					->addColor( '100', __( 'Base 100', 'kenta' ), $defaults['base_100'] )
					->addColor( 'default', __( 'Base Color', 'kenta' ), $defaults['base_color'] )
				,
			];
		}

	}
}