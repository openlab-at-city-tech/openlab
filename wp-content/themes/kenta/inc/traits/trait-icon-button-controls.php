<?php
/**
 * Icon Button trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Icon_Button_Controls' ) ) {

	/**
	 * Icon Button controls
	 */
	trait Kenta_Icon_Button_Controls {

		protected function getIconControls( $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'size'     => '14px',
				'selector' => '',
			] );

			$controls = [
				( new Slider( $this->getSlug( 'icon_button_size' ) ) )
					->setLabel( __( 'Icon Size', 'kenta' ) )
					->enableResponsive()
					->asyncCss( $defaults['selector'], [
						'--kenta-icon-button-size' => 'value',
						'font-size'                => 'value'
					] )
					->setDefaultValue( $defaults['size'] )
					->setMin( 5 )
					->setMax( 50 )
					->setDefaultUnit( 'px' )
				,
			];

			return apply_filters( 'kenta_icon_button_controls', $controls, $this->getSlug(), $defaults );
		}

		protected function getIconStyleControls( $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'preset'                => 'default',
				'render-callback'       => [ null ],
				'css-selective-refresh' => '',
				'selector'              => '',
				'shape'                 => 'none',
				'fill'                  => 'solid',
				'icon-initial'          => 'var(--kenta-accent-color)',
				'icon-hover'            => 'var(--kenta-primary-color)',
				'bg-initial'            => 'var(--kenta-primary-color)',
				'bg-hover'              => 'var(--kenta-accent-color)',
				'border-initial'        => 'var(--kenta-accent-color)',
				'border-hover'          => 'var(--kenta-primary-color)',
			] );

			$controls = [
				( new Select( $this->getSlug( 'icon_button_preset' ) ) )
					->setLabel( __( 'Icon Style Preset', 'kenta' ) )
					->selectiveRefresh( ...$defaults['render-callback'] )
					->bindSelectiveRefresh( $defaults['css-selective-refresh'] )
					->setDefaultValue( $defaults['preset'] )
					->setChoices( apply_filters( 'kenta_icon_button_preset_options', [
						'ghost'           => __( 'Ghost', 'kenta' ),
						'square-solid'    => __( 'Square Solid', 'kenta' ),
						'square-outline'  => __( 'Square Outline', 'kenta' ),
						'rounded-solid'   => __( 'Rounded Solid', 'kenta' ),
						'rounded-outline' => __( 'Rounded Outline', 'kenta' ),
						'custom'          => __( 'Custom (Premium)', 'kenta' ),
					] ) )
				,
			];

			return apply_filters( 'kenta_icon_button_style_controls', $controls, $this->getSlug(), $defaults );
		}

		protected function getIconButtonPreset( $preset ) {
			$solid = [
				$this->getSlug( 'icon_button_shape_fill_type' ) => 'solid',
				$this->getSlug( 'icon_button_icon_color' )      => [
					'initial' => 'var(--kenta-base-color)',
					'hover'   => 'var(--kenta-base-color)',
				],
				$this->getSlug( 'icon_button_bg_color' )        => [
					'initial' => 'var(--kenta-primary-color)',
					'hover'   => 'var(--kenta-accent-color)',
				],
				$this->getSlug( 'icon_button_border_color' )    => [
					'initial' => 'var(--kenta-primary-color)',
					'hover'   => 'var(--kenta-accent-color)',
				],
			];

			$outline = [
				$this->getSlug( 'icon_button_shape_fill_type' ) => 'outline',
				$this->getSlug( 'icon_button_icon_color' )      => [
					'initial' => 'var(--kenta-accent-color)',
					'hover'   => 'var(--kenta-primary-color)',
				],
				$this->getSlug( 'icon_button_bg_color' )        => [
					'initial' => 'var(--kenta-primary-color)',
					'hover'   => 'var(--kenta-accent-color)',
				],
				$this->getSlug( 'icon_button_border_color' )    => [
					'initial' => 'var(--kenta-accent-color)',
					'hover'   => 'var(--kenta-primary-color)',
				],
			];

			$presets = [
				'ghost'           => array_merge( $outline, [
					$this->getSlug( 'icon_button_icon_shape' ) => 'none',
				] ),
				'square-solid'    => array_merge( $solid, [
					$this->getSlug( 'icon_button_icon_shape' ) => 'square',
				] ),
				'square-outline'  => array_merge( $outline, [
					$this->getSlug( 'icon_button_icon_shape' ) => 'square',
				] ),
				'rounded-solid'   => array_merge( $solid, [
					$this->getSlug( 'icon_button_icon_shape' ) => 'rounded',
				] ),
				'rounded-outline' => array_merge( $outline, [
					$this->getSlug( 'icon_button_icon_shape' ) => 'rounded',
				] ),
			];

			return $presets[ $preset ] ?? [];
		}

		public function getIconButtonCss() {
			$preset = $this->getIconButtonPreset( CZ::get( $this->getSlug( 'icon_button_preset' ) ) );

			return array_merge(
				Css::colors( CZ::get( $this->getSlug( 'icon_button_icon_color' ), $preset ), [
					'initial' => '--kenta-icon-button-icon-initial-color',
					'hover'   => '--kenta-icon-button-icon-hover-color',
				] ),
				Css::colors( CZ::get( $this->getSlug( 'icon_button_bg_color' ), $preset ), [
					'initial' => '--kenta-icon-button-bg-initial-color',
					'hover'   => '--kenta-icon-button-bg-hover-color',
				] ),
				Css::colors( CZ::get( $this->getSlug( 'icon_button_border_color' ), $preset ), [
					'initial' => '--kenta-icon-button-border-initial-color',
					'hover'   => '--kenta-icon-button-border-hover-color',
				] ),
				[
					'--kenta-icon-button-size' => CZ::get( $this->getSlug( 'icon_button_size' ) ),
					'font-size'                => CZ::get( $this->getSlug( 'icon_button_size' ) )
				]
			);
		}
	}
}
