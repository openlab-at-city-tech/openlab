<?php
/**
 * Post card trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Toggle;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Post_Card' ) ) {

	/**
	 * Post card functions
	 */
	trait Kenta_Post_Card {

		/**
		 * @param string $prefix
		 * @param array $defaults
		 *
		 * @return array
		 */
		protected function getCardContentControls( $prefix = '', $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'selector'          => '.card',
				'scroll-reveal'     => 'yes',
				'content-spacing'   => '24px',
				'thumbnail-spacing' => '0px',
				'text'              => 'left',
				'vertical'          => 'center',
				'thumb-motion'      => 'yes',
			] );

			return [
				( new Toggle( $prefix . 'card_scroll_reveal' ) )
					->setLabel( __( 'Enable Scroll Reveal', 'kenta' ) )
					->setDefaultValue( $defaults['scroll-reveal'] )
				,
				( new Slider( $prefix . 'card_content_spacing' ) )
					->setLabel( __( 'Content Spacing', 'kenta' ) )
					->asyncCss( $defaults['selector'], [ '--card-content-spacing' => 'value' ] )
					->enableResponsive()
					->setDefaultUnit( 'px' )
					->setDefaultValue( $defaults['content-spacing'] )
				,
				( new Slider( $prefix . 'card_thumbnail_spacing' ) )
					->setLabel( __( 'Thumbnail Spacing', 'kenta' ) )
					->asyncCss( $defaults['selector'], [ '--card-thumbnail-spacing' => 'value' ] )
					->enableResponsive()
					->setDefaultUnit( 'px' )
					->setDefaultValue( $defaults['thumbnail-spacing'] )
				,
				( new Separator() ),
				( new ImageRadio( $prefix . 'card_content_alignment' ) )
					->setLabel( __( 'Content Alignment', 'kenta' ) )
					->asyncCss( $defaults['selector'], [ 'text-align' => 'value' ] )
					->enableResponsive()
					->inlineChoices()
					->setDefaultValue( $defaults['text'] )
					->setChoices( [
						'left'   => [
							'src'   => kenta_image( 'text-left' ),
							'title' => __( 'Left', 'kenta' )
						],
						'center' => [
							'src'   => kenta_image( 'text-center' ),
							'title' => __( 'Center', 'kenta' )
						],
						'right'  => [
							'src'   => kenta_image( 'text-right' ),
							'title' => __( 'Right', 'kenta' )
						],
					] )
				,
			];
		}

		protected function getCardStyleControls( $prefix = '', $defaults = [] ) {

			$defaults = wp_parse_args( $defaults, [
				'preset'    => 'plain',
				'selective' => ''
			] );

			return [
				( new Select( $prefix . 'card_style_preset' ) )
					->setLabel( __( 'Card Style', 'kenta' ) )
					->setDefaultValue( $defaults['preset'] )
					->bindSelectiveRefresh( $defaults['selective'] )
					->setChoices( kenta_card_style_preset_options() )
				,
			];
		}
	}
}
