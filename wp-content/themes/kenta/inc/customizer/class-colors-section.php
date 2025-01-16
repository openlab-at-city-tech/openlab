<?php
/**
 * Colors customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ColorPalettes;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Section;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Colors_Section' ) ) {

	class Kenta_Colors_Section extends Section {

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new \LottaFramework\Customizer\Controls\Tabs( 'kenta_color_scheme' ) )
					->setActiveTab( 'light' )
					->addTab( 'light', __( 'Light', 'kenta' ), $this->getColors( 'light', kenta_color_presets() ) )
					->addTab( 'dark', __( 'Dark', 'kenta' ), $this->getColors( 'dark', kenta_dark_color_presets(), [
						'primary-color'  => \LottaFramework\Facades\Css::INITIAL_VALUE,
						'primary-active' => \LottaFramework\Facades\Css::INITIAL_VALUE,
					] ) ),
				kenta_docs_control(
					__( '%sRead Colors Documentation%s', 'kenta' ),
					'https://kentatheme.com/docs/kenta-theme/general-theme-options/colors/',
					'kenta_colors_doc'
				)->hideBackground(),
				( new Separator( 'kenta_dark_color_palette_separator' ) ),
				( new Toggle( 'kenta_default_dark_scheme' ) )
					->setLabel( __( 'Use Dark Scheme As Default', 'kenta' ) )
					->closeByDefault()
				,
				( new Toggle( 'kenta_save_color_scheme' ) )
					->setLabel( __( 'Save User Color Scheme', 'kenta' ) )
					->setDescription( __( "Save the user's color scheme to the cookie and refresh the page without losing current color scheme.", 'kenta' ) )
					->openByDefault()
				,
			];
		}

		protected function getColors( $scheme, $palettes = [], $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'primary-color'  => "var(--kenta-{$scheme}-primary-color)",
				'primary-active' => "var(--kenta-{$scheme}-primary-active)",
				'accent-color'   => "var(--kenta-{$scheme}-accent-color)",
				'accent-active'  => "var(--kenta-{$scheme}-accent-active)",
				'base-300'       => "var(--kenta-{$scheme}-base-300)",
				'base-200'       => "var(--kenta-{$scheme}-base-200)",
				'base-100'       => "var(--kenta-{$scheme}-base-100)",
				'base-color'     => "var(--kenta-{$scheme}-base-color)",
			] );

			$prefix = $scheme === 'light' ? 'kenta' : "kenta_{$scheme}";

			$palettesControl = ( new ColorPalettes( "{$prefix}_color_palettes", [
				'kenta-primary-color'  => __( 'Primary Color', 'kenta' ),
				'kenta-primary-active' => __( 'Primary Active', 'kenta' ),
				'kenta-accent-color'   => __( 'Accent Color', 'kenta' ),
				'kenta-accent-active'  => __( 'Accent Active', 'kenta' ),
				'kenta-base-color'     => __( 'Base Color', 'kenta' ),
				'kenta-base-50'        => __( 'Base 50', 'kenta' ),
				'kenta-base-100'       => __( 'Base 100', 'kenta' ),
				'kenta-base-200'       => __( 'Base 200', 'kenta' ),
				'kenta-base-300'       => __( 'Base 300', 'kenta' ),
			] ) )
				->setLabel( __( 'Color Presets', 'kenta' ) )
				->setColor( "{$prefix}_primary_color", [
					'default' => 'kenta-primary-color',
					'active'  => 'kenta-primary-active',
				] )
				->setColor( "{$prefix}_accent_color", [
					'default' => 'kenta-accent-color',
					'active'  => 'kenta-accent-active',
				] )
				->setColor( "{$prefix}_base_color", [
					'default' => 'kenta-base-color',
					'100'     => 'kenta-base-100',
					'200'     => 'kenta-base-200',
					'300'     => 'kenta-base-300',
				] )
				->bindSelectiveRefresh( 'kenta-global-selective-css' )
				->setDefaultValue( 'preset-1' );

			foreach ( $palettes as $id => $preset ) {
				$palettesControl->addPalette( $id, $preset );
			}

			return [
				$palettesControl,
				( new ColorPicker( "{$prefix}_primary_color" ) )
					->setLabel( __( 'Primary Color', 'kenta' ) )
					->enableAlpha()
					->computedValue()
					->setSwatches( [] )
					->asyncColors( ':root', [
						'default' => "--kenta-{$scheme}-primary-color",
						'active'  => "--kenta-{$scheme}-primary-active",
					] )
					->setCustomizerColors( ':root', [
						'default' => "--kenta-{$scheme}-primary-color",
						'active'  => "--kenta-{$scheme}-primary-active",
					] )
					->addColor( 'default', __( 'Default', 'kenta' ), $defaults['primary-color'] )
					->addColor( 'active', __( 'Active', 'kenta' ), $defaults['primary-active'] )
				,
				( new ColorPicker( "{$prefix}_accent_color" ) )
					->setLabel( __( 'Accent Color', 'kenta' ) )
					->enableAlpha()
					->computedValue()
					->setSwatches( [] )
					->asyncColors( ':root', [
						'default' => "--kenta-{$scheme}-accent-color",
						'active'  => "--kenta-{$scheme}-accent-active",
					] )
					->setCustomizerColors( ':root', [
						'default' => "--kenta-{$scheme}-accent-color",
						'active'  => "--kenta-{$scheme}-accent-active",
					] )
					->addColor( 'default', __( 'Default', 'kenta' ), $defaults['accent-color'] )
					->addColor( 'active', __( 'Active', 'kenta' ), $defaults['accent-active'] )
				,
				( new ColorPicker( "{$prefix}_base_color" ) )
					->setLabel( __( 'Base Color', 'kenta' ) )
					->enableAlpha()
					->computedValue()
					->setSwatches( [] )
					->asyncColors( ':root', [
						'default' => "--kenta-{$scheme}-base-color",
						'100'     => "--kenta-{$scheme}-base-100",
						'200'     => "--kenta-{$scheme}-base-200",
						'300'     => "--kenta-{$scheme}-base-300",
					] )
					->setCustomizerColors( ':root', [
						'default' => "--kenta-{$scheme}-base-color",
						'100'     => "--kenta-{$scheme}-base-100",
						'200'     => "--kenta-{$scheme}-base-200",
						'300'     => "--kenta-{$scheme}-base-300",
					] )
					->addColor( '300', __( 'Base 300', 'kenta' ), $defaults['base-300'] )
					->addColor( '200', __( 'Base 200', 'kenta' ), $defaults['base-200'] )
					->addColor( '100', __( 'Base 100', 'kenta' ), $defaults['base-100'] )
					->addColor( 'default', __( 'Base Color', 'kenta' ), $defaults['base-color'] )
				,
			];
		}
	}
}

