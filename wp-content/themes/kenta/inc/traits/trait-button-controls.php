<?php
/**
 * Button trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Button_Controls' ) ) {

	/**
	 * Button controls
	 */
	trait Kenta_Button_Controls {

		protected function getButtonContentControls( $id = '', $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'button-selector'  => '',
				'button-selective' => [],
				'label'            => __( 'Button', 'kenta' ),
				'show-arrow'       => 'no',
				'arrow-dir'        => 'left',
				'arrow'            => [
					'library' => 'fa-solid',
					'value'   => 'fas fa-star',
				],
			] );

			return [
				( new Text( $id . 'text' ) )
					->setLabel( __( 'Label', 'kenta' ) )
					->asyncText( $defaults['button-selector'] . ' .kenta-button-text' )
					->displayInline()
					->setDefaultValue( $defaults['label'] )
				,
				( new Toggle( $id . 'show_arrow' ) )
					->setLabel( __( 'Show Icon', 'kenta' ) )
					->selectiveRefresh( $defaults['button-selector'] . ' .kenta-button-icon', function () use ( $id ) {
						if ( CZ::checked( $id . 'show_arrow' ) ) {
							IconsManager::print( CZ::get( $id . 'arrow' ) );
						}
					} )
					->setDefaultValue( $defaults['show-arrow'] )
				,
				( new Condition() )
					->setCondition( [ $id . 'show_arrow' => 'yes' ] )
					->setControls( [
						( new Icons( $id . 'arrow' ) )
							->setLabel( __( 'Choose Icon', 'kenta' ) )
							->selectiveRefresh( $defaults['button-selector'] . ' .kenta-button-icon', function () use ( $id ) {
								IconsManager::print( CZ::get( $id . 'arrow' ) );
							} )
							->setDefaultValue( $defaults['arrow'] )
						,
						( new Radio( $id . 'arrow_dir' ) )
							->setLabel( __( 'Icon Direction', 'kenta' ) )
							->selectiveRefresh( ...$defaults['button-selective'] )
							->setDefaultValue( $defaults['arrow-dir'] )
							->buttonsGroupView()
							->setChoices( [
								'left'  => __( 'Left', 'kenta' ),
								'right' => __( 'Right', 'kenta' )
							] )
						,
					] )
				,
			];
		}

		/**
		 * @param string $id
		 * @param array $defaults
		 *
		 * @return array
		 */
		protected function getButtonStyleControls( $id = '', $defaults = [] ) {

			$defaults = wp_parse_args( $defaults, [
				'button-selector'      => null,
				'button-css-selective' => '',
				'preset'               => 'solid',
				'text-initial'         => 'var(--kenta-base-color)',
				'text-hover'           => 'var(--kenta-base-color)',
				'button-initial'       => 'var(--kenta-primary-color)',
				'button-hover'         => 'var(--kenta-accent-color)',
				'border-initial'       => 'var(--kenta-primary-color)',
				'border-hover'         => 'var(--kenta-accent-color)',
				'min-height'           => '32px',
				'border'               => [ 1, 'solid' ],
				'preset-options'       => [
					'ghost'   => __( 'Ghost', 'kenta' ),
					'solid'   => __( 'Solid', 'kenta' ),
					'outline' => __( 'Outline', 'kenta' ),
					'invert'  => __( 'Invert', 'kenta' ),
					'primary' => __( 'Primary', 'kenta' ),
					'accent'  => __( 'Accent', 'kenta' ),
					'custom'  => __( 'Custom (Premium)', 'kenta' ),
				],
				'shadow'               => [
					'var(--kenta-primary-color)',
					'0px',
					'5px',
					'10px',
					'-5px',
					false
				],
				'shadow-active'        => [
					'var(--kenta-primary-color)',
					'0px',
					'5px',
					'10px',
					'-5px',
					true
				],
				'typography'           => [
					'family'        => 'inherit',
					'fontSize'      => '0.75rem',
					'variant'       => '500',
					'lineHeight'    => '1',
					'textTransform' => 'capitalize'
				],
				'border-radius'        => [
					'linked' => true,
					'left'   => '2px',
					'right'  => '2px',
					'top'    => '2px',
					'bottom' => '2px',
				],
				'padding'              => [
					'top'    => '0.85em',
					'right'  => '1.25em',
					'bottom' => '0.85em',
					'left'   => '1.25em',
				],
			] );

			$selector = $defaults['button-selector'];

			$controls = [
				( new Slider( $id . 'min_height' ) )
					->setLabel( __( 'Min Height', 'kenta' ) )
					->asyncCss( $selector, [ '--kenta-button-height' => 'value' ] )
					->enableResponsive()
					->setMin( 30 )
					->setMax( 100 )
					->setDefaultUnit( 'px' )
					->setDefaultValue( $defaults['min-height'] )
				,
				( new Separator() ),
				( new Select( $id . 'preset' ) )
					->setLabel( __( 'Button Style Presets', 'kenta' ) )
					->bindSelectiveRefresh( $defaults['button-css-selective'] )
					->setDefaultValue( $defaults['preset'] )
					->setChoices( apply_filters( 'kenta_button_preset_options', $defaults['preset-options'] ) )
				,
			];

			return apply_filters( 'kenta_button_style_controls', $controls, $id, $defaults );
		}
	}
}
